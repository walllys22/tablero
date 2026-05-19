<?php

namespace App\Http\Controllers;

use App\Models\InscripcionCompetidor;
use App\Models\InscripcionCompetidorModalidad;
use App\Models\InscripcionOrganizacion;
use App\Models\Competidor;
use App\Models\Organizacion;
use App\Models\Persona;
use App\Models\SorteoLlave;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InscripcionController extends Controller
{
    public function index(Torneo $torneo)
    {
        $torneo->load('persona');
        $personas = Persona::where('status', 1)->orderBy('first_name')->get();
        $organizaciones = Organizacion::with('persona')
            ->where('status', 1)
            ->whereDoesntHave('inscripciones', function ($query) use ($torneo) {
                $query->where('torneo_id', $torneo->id);
            })
            ->orderBy('nombre')
            ->get();
        $categorias = $torneo->categorias()
            ->with('modalidad')
            ->leftJoin('modalidades', 'modalidades.id', '=', 'categorias.modalidad_id')
            ->orderBy('categorias.nombre')
            ->orderBy('modalidades.nombre')
            ->select('categorias.*')
            ->get();
        $organizacionesInscritas = $torneo->inscripcionOrganizaciones()
            ->with('organizacion.persona')
            ->join('organizaciones', 'organizaciones.id', '=', 'inscripcion_organizaciones.organizacion_id')
            ->orderBy('organizaciones.nombre')
            ->select('inscripcion_organizaciones.*')
            ->get();
        $modalidades = $torneo->modalidades()
            ->with(['categorias' => function ($query) {
                $query->orderBy('nombre');
            }])
            ->orderBy('nombre')
            ->get();

        return view('inscripciones.browse', compact('torneo', 'personas', 'organizaciones', 'categorias', 'organizacionesInscritas', 'modalidades'));
    }

    public function ajaxList(Request $request, Torneo $torneo)
    {
        $torneo->load('persona');
        $paginate = (int) $request->input('paginate', 10);
        $paginate = in_array($paginate, [10, 25, 50, 100], true) ? $paginate : 10;
        $search = trim((string) $request->input('search', ''));

        $organizaciones = $torneo->inscripcionOrganizaciones()
            ->with(['organizacion.persona', 'competidores.persona', 'competidores.modalidades.modalidad', 'competidores.modalidades.categoria'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->whereHas('organizacion', function ($query) use ($search) {
                        $query->where('nombre', 'like', "%{$search}%");
                    })->orWhereHas('competidores.persona', function ($query) use ($search) {
                        $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('ci', 'like', "%{$search}%");
                    });
                });
            })
            ->orderByDesc('id')
            ->paginate($paginate)
            ->withQueryString();

        return view('inscripciones.list', compact('organizaciones', 'torneo', 'search'));
    }

    public function print(Request $request, Torneo $torneo)
    {
        $modalidadId = $request->filled('modalidad_id') ? (int) $request->input('modalidad_id') : null;
        $categoriaId = $request->filled('categoria_id') ? (int) $request->input('categoria_id') : null;

        $detalles = InscripcionCompetidorModalidad::query()
            ->with([
                'modalidad',
                'categoria',
                'inscripcionCompetidor.persona',
                'inscripcionCompetidor.inscripcionOrganizacion.organizacion',
            ])
            ->whereHas('inscripcionCompetidor', function ($query) use ($torneo) {
                $query->where('torneo_id', $torneo->id);
            })
            ->when($modalidadId, function ($query) use ($modalidadId) {
                $query->where('inscripcion_competidor_modalidades.modalidad_id', $modalidadId);
            })
            ->when($categoriaId, function ($query) use ($categoriaId) {
                $query->where('inscripcion_competidor_modalidades.categoria_id', $categoriaId);
            })
            ->join('modalidades', 'modalidades.id', '=', 'inscripcion_competidor_modalidades.modalidad_id')
            ->join('categorias', 'categorias.id', '=', 'inscripcion_competidor_modalidades.categoria_id')
            ->orderBy('modalidades.nombre')
            ->orderBy('categorias.edad_desde')
            ->orderBy('categorias.edad_hasta')
            ->orderBy('categorias.genero')
            ->orderBy('categorias.peso_hasta')
            ->orderBy('categorias.nombre')
            ->select('inscripcion_competidor_modalidades.*')
            ->get();

        $modalidades = $detalles
            ->groupBy('modalidad_id')
            ->map(function ($items) {
                return [
                    'modalidad' => $items->first()->modalidad,
                    'categorias' => $items->groupBy('categoria_id')->map(function ($categoriaItems) {
                        return [
                            'categoria' => $categoriaItems->first()->categoria,
                            'competidores' => $categoriaItems,
                        ];
                    })->values(),
                ];
            })
            ->values();

        return view('inscripciones.print', compact('torneo', 'modalidades'));
    }

    public function printParticipantes(Request $request, Torneo $torneo, InscripcionOrganizacion $inscripcion)
    {
        abort_unless($inscripcion->torneo_id === $torneo->id, 404);

        $modalidadId = $request->filled('modalidad_id') ? (int) $request->input('modalidad_id') : null;
        $categoriaId = $request->filled('categoria_id') ? (int) $request->input('categoria_id') : null;

        $torneo->load('persona');
        $inscripcion->load('organizacion');

        $competidores = $inscripcion->competidores()
            ->with(['persona', 'modalidades.modalidad', 'modalidades.categoria'])
            ->when($modalidadId || $categoriaId, function ($query) use ($modalidadId, $categoriaId) {
                $query->whereHas('modalidades', function ($query) use ($modalidadId, $categoriaId) {
                    $query
                        ->when($modalidadId, fn ($query) => $query->where('modalidad_id', $modalidadId))
                        ->when($categoriaId, fn ($query) => $query->where('categoria_id', $categoriaId));
                });
            })
            ->get()
            ->map(function ($competidor) use ($modalidadId, $categoriaId) {
                if ($modalidadId || $categoriaId) {
                    $competidor->setRelation('modalidades', $competidor->modalidades->filter(function ($detalle) use ($modalidadId, $categoriaId) {
                        return (! $modalidadId || (int) $detalle->modalidad_id === $modalidadId)
                            && (! $categoriaId || (int) $detalle->categoria_id === $categoriaId);
                    })->values());
                }

                return $competidor;
            })
            ->sortBy(fn ($competidor) => $competidor->persona->first_name ?? '')
            ->values();

        $pesosCompetidores = $inscripcion->organizacion->competidores()
            ->pluck('peso', 'persona_id');

        return view('inscripciones.participantes_print', compact(
            'torneo',
            'inscripcion',
            'competidores',
            'pesosCompetidores'
        ));
    }

    public function storeOrganizacion(Request $request, Torneo $torneo)
    {
        $data = $request->validate([
            'organizacion_id' => ['required', Rule::exists('organizaciones', 'id')],
            'costo' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        $exists = InscripcionOrganizacion::where('torneo_id', $torneo->id)
            ->where('organizacion_id', $data['organizacion_id'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['organizacion_id' => 'La organizacion ya esta inscrita en este campeonato.'])
                ->withInput(['creating_organizacion' => 1] + $data);
        }

        $inscripcion = $torneo->inscripcionOrganizaciones()->create([
            'organizacion_id' => $data['organizacion_id'],
            'costo' => $data['costo'],
        ]);

        return redirect()
            ->route('inscripciones.index', $torneo)
            ->with('status', 'Organizacion inscrita correctamente.')
            ->with('recibo_inscripcion_id', $inscripcion->id);
    }

    public function destroyOrganizacion(Torneo $torneo, InscripcionOrganizacion $inscripcion)
    {
        abort_unless($inscripcion->torneo_id === $torneo->id, 404);

        $inscripcion->delete();

        return redirect()
            ->route('inscripciones.index', $torneo)
            ->with('status', 'Organizacion eliminada de la inscripcion correctamente.');
    }

    public function pagarOrganizacion(Request $request, Torneo $torneo, InscripcionOrganizacion $inscripcion)
    {
        abort_unless($inscripcion->torneo_id === $torneo->id, 404);

        $data = $request->validate([
            'monto_pago' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
        ]);

        $inscripcion->load('competidores.modalidades');

        $totalCompetidores = $inscripcion->competidores->sum(function ($competidor) {
            return $competidor->modalidades->sum('costo');
        });
        $totalDeuda = (float) $inscripcion->costo + (float) $totalCompetidores;
        $montoPagadoActual = (float) ($inscripcion->monto_pagado ?? 0);
        $pendientePago = max(0, $totalDeuda - $montoPagadoActual);
        $montoPago = min((float) $data['monto_pago'], $pendientePago);

        $inscripcion->update([
            'monto_pagado' => $montoPagadoActual + $montoPago,
        ]);

        return redirect()
            ->route('inscripciones.index', $torneo)
            ->with('status', 'Pago de la organizacion registrado correctamente.');
    }

    public function storeCompetidor(Request $request, Torneo $torneo)
    {
        if (! $torneo->inscripcionOrganizaciones()->exists()) {
            return back()
                ->withErrors(['inscripcion_organizacion_id' => 'Primero inscriba una organizacion al campeonato.'])
                ->withInput(['creating_competidor' => 1] + $request->all());
        }

        $data = $request->validate([
            'inscripcion_organizacion_id' => [
                'required',
                Rule::exists('inscripcion_organizaciones', 'id')->where('torneo_id', $torneo->id),
            ],
            'persona_id' => ['required', Rule::exists('personas', 'id')],
            'modalidades' => ['required', 'array', 'min:1'],
            'modalidades.*.id' => [
                'required',
                'distinct',
                Rule::exists('modalidades', 'id')->where('torneo_id', $torneo->id),
            ],
            'modalidades.*.categoria_id' => [
                'required',
                'distinct',
                Rule::exists('categorias', 'id')->where('torneo_id', $torneo->id),
            ],
            'modalidades.*.costo' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        $alreadyRegistered = InscripcionCompetidor::where('torneo_id', $torneo->id)
            ->where('persona_id', $data['persona_id'])
            ->exists();

        if ($alreadyRegistered) {
            return back()
                ->withErrors(['persona_id' => 'Este competidor ya esta inscrito en este campeonato.'])
                ->withInput(['creating_competidor' => 1] + $request->all());
        }

        DB::transaction(function () use ($data, $torneo) {
            $competidor = InscripcionCompetidor::create([
                'torneo_id' => $torneo->id,
                'inscripcion_organizacion_id' => $data['inscripcion_organizacion_id'],
                'persona_id' => $data['persona_id'],
            ]);

            foreach ($data['modalidades'] as $modalidad) {
                $competidor->modalidades()->create([
                    'modalidad_id' => $modalidad['id'],
                    'categoria_id' => $modalidad['categoria_id'],
                    'costo' => $modalidad['costo'],
                ]);
            }
        });

        return redirect()
            ->route('inscripciones.index', $torneo)
            ->with('status', 'Competidor inscrito correctamente.');
    }

    public function participantes(Request $request, Torneo $torneo, InscripcionOrganizacion $inscripcion)
    {
        abort_unless($inscripcion->torneo_id === $torneo->id, 404);

        $torneo->load('persona');
        $inscripcion->load('organizacion.persona');
        $categoriasDisponibles = $this->categoriasQuery($torneo, $request)->get();
        $categoriaSeleccionada = $request->filled('categoria_id')
            ? $categoriasDisponibles->firstWhere('id', (int) $request->input('categoria_id'))
            : null;
        $personasNoDisponibles = collect();
        $filtrarPeso = $categoriaSeleccionada && $this->categoriaRequierePeso($categoriaSeleccionada);
        $pesoEsMinimo = $filtrarPeso && $this->categoriaPesoEsMinimo($categoriaSeleccionada);

        // Con una categoria especifica, no mostramos competidores que ya estan
        // inscritos en esa misma categoria del campeonato.
        if ($categoriaSeleccionada) {
            $personasNoDisponibles = InscripcionCompetidor::where('torneo_id', $torneo->id)
                ->whereHas('modalidades', function ($query) use ($categoriaSeleccionada) {
                    $query->where('categoria_id', $categoriaSeleccionada->id);
                })
                ->pluck('persona_id');
        }

        $personas = $inscripcion->organizacion->competidores()
            ->with('persona')
            ->where('competidores.status', 1)
            ->when($filtrarPeso, function ($query) use ($categoriaSeleccionada, $pesoEsMinimo) {
                $query->whereNotNull('competidores.peso')
                    ->when($pesoEsMinimo, function ($query) use ($categoriaSeleccionada) {
                        $query->where('competidores.peso', '>=', $categoriaSeleccionada->peso_hasta);
                    }, function ($query) use ($categoriaSeleccionada) {
                        $query->where('competidores.peso', '<=', $categoriaSeleccionada->peso_hasta);
                    });
            })
            ->whereHas('persona', function ($query) use ($categoriaSeleccionada, $personasNoDisponibles) {
                $query->where('status', 1)
                    ->when($categoriaSeleccionada && $categoriaSeleccionada->genero && $categoriaSeleccionada->genero !== 'Mixto', function ($query) use ($categoriaSeleccionada) {
                        $query->where('gender', $categoriaSeleccionada->genero);
                    })
                    ->when($categoriaSeleccionada && $this->categoriaTieneEdadExacta($categoriaSeleccionada), function ($query) use ($categoriaSeleccionada) {
                        $edad = (int) $categoriaSeleccionada->edad_desde;

                        $query->whereDate('birth_date', '<=', now()->subYears($edad)->toDateString())
                            ->whereDate('birth_date', '>=', now()->subYears($edad + 1)->addDay()->toDateString());
                    })
                    ->when($categoriaSeleccionada && ! $this->categoriaTieneEdadExacta($categoriaSeleccionada) && $categoriaSeleccionada->edad_desde !== null, function ($query) use ($categoriaSeleccionada) {
                        $query->whereDate('birth_date', '<=', now()->subYears((int) $categoriaSeleccionada->edad_desde)->toDateString());
                    })
                    ->when($categoriaSeleccionada && ! $this->categoriaTieneEdadExacta($categoriaSeleccionada) && $categoriaSeleccionada->edad_hasta !== null, function ($query) use ($categoriaSeleccionada) {
                        $query->whereDate('birth_date', '>=', now()->subYears(((int) $categoriaSeleccionada->edad_hasta) + 1)->addDay()->toDateString());
                    })
                    ->when($personasNoDisponibles->isNotEmpty(), function ($query) use ($personasNoDisponibles) {
                        $query->whereNotIn('id', $personasNoDisponibles);
                    });
            })
            ->join('personas', 'personas.id', '=', 'competidores.persona_id')
            ->orderBy('personas.first_name')
            ->select('competidores.*')
            ->get()
            ->pluck('persona')
            ->filter()
            ->values();
        $categoriaIdsSorteadas = SorteoLlave::where('torneo_id', $torneo->id)
            ->pluck('categoria_id');
        $categorias = $torneo->categorias()
            ->with('modalidad')
            ->whereNotIn('categorias.id', $categoriaIdsSorteadas)
            ->when($request->filled('modalidad_id'), function ($query) use ($request) {
                $query->where('modalidad_id', $request->input('modalidad_id'));
            })
            ->orderBy('nombre')
            ->get();
        $modalidades = $torneo->modalidades()->orderBy('nombre')->get();
        $modalidadSeleccionada = $request->filled('modalidad_id')
            ? $modalidades->firstWhere('id', (int) $request->input('modalidad_id'))
            : null;
        $mostrarPesoFiltro = $modalidadSeleccionada
            ? str_contains(mb_strtolower((string) $modalidadSeleccionada->nombre), 'kumite')
            : false;
        $pesosCompetidores = $inscripcion->organizacion->competidores()
            ->pluck('peso', 'persona_id');
        $competidores = $inscripcion->competidores()
            ->with(['persona', 'modalidades.modalidad', 'modalidades.categoria'])
            ->latest()
            ->get();

        return view('inscripciones.participantes', compact(
            'torneo',
            'inscripcion',
            'personas',
            'categoriasDisponibles',
            'categorias',
            'modalidades',
            'mostrarPesoFiltro',
            'pesosCompetidores',
            'competidores'
        ));
    }

    public function storeParticipante(Request $request, Torneo $torneo, InscripcionOrganizacion $inscripcion)
    {
        abort_unless($inscripcion->torneo_id === $torneo->id, 404);

        $competidoresOrganizacion = $inscripcion->organizacion->competidores()
            ->where('status', 1)
            ->get()
            ->keyBy('persona_id');
        $personaIdsOrganizacion = $competidoresOrganizacion->keys()->all();

        $data = $request->validate([
            'persona_ids' => ['required', 'array', 'min:1'],
            'persona_ids.*' => ['required', 'distinct', Rule::exists('personas', 'id'), Rule::in($personaIdsOrganizacion)],
            'modalidades' => ['required', 'array', 'min:1'],
            'modalidades.*.id' => [
                'required',
                'distinct',
                Rule::exists('modalidades', 'id')->where('torneo_id', $torneo->id),
            ],
            'modalidades.*.categoria_id' => [
                'required',
                'distinct',
                Rule::exists('categorias', 'id')->where('torneo_id', $torneo->id),
            ],
            'modalidades.*.costo' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        $personas = Persona::whereIn('id', $data['persona_ids'])
            ->get()
            ->keyBy('id');
        $categoriasSeleccionadas = $torneo->categorias()
            ->whereIn('id', collect($data['modalidades'])->pluck('categoria_id'))
            ->get()
            ->keyBy('id');
        $categoriaIdsSorteadas = SorteoLlave::where('torneo_id', $torneo->id)
            ->pluck('categoria_id')
            ->map(fn ($id) => (int) $id);

        foreach ($data['modalidades'] as $modalidad) {
            $categoria = $categoriasSeleccionadas->get($modalidad['categoria_id']);

            if (! $categoria || (int) $categoria->modalidad_id !== (int) $modalidad['id']) {
                return back()
                    ->withErrors(['modalidades' => 'La categoria seleccionada no corresponde a la modalidad.'])
                    ->withInput();
            }

            if ($categoriaIdsSorteadas->contains((int) $categoria->id)) {
                return back()
                    ->withErrors(['modalidades' => 'No se puede inscribir participantes en una categoria que ya fue sorteada.'])
                    ->withInput();
            }

            if ($categoria->genero && $categoria->genero !== 'Mixto') {
                $invalidPerson = $personas->first(function ($persona) use ($categoria) {
                    return $persona->gender !== $categoria->genero;
                });

                if ($invalidPerson) {
                    return back()
                        ->withErrors(['persona_ids' => 'Todos los competidores deben ser del mismo sexo que la categoria seleccionada.'])
                        ->withInput();
                }
            }

            if ($categoria->edad_desde !== null || $categoria->edad_hasta !== null) {
                $invalidPerson = $personas->first(function ($persona) use ($categoria) {
                    if (! $persona->birth_date) {
                        return true;
                    }

                    $age = $persona->birth_date->diffInYears(now());

                    if ($this->categoriaTieneEdadExacta($categoria)) {
                        return $age !== (int) $categoria->edad_desde;
                    }

                    return ($categoria->edad_desde !== null && $age < (int) $categoria->edad_desde)
                        || ($categoria->edad_hasta !== null && $age > (int) $categoria->edad_hasta);
                });

                if ($invalidPerson) {
                    return back()
                        ->withErrors(['persona_ids' => 'Todos los competidores deben estar dentro del rango de edad de la categoria seleccionada.'])
                        ->withInput();
                }
            }

            if ($this->categoriaRequierePeso($categoria)) {
                $invalidPerson = $personas->first(function ($persona) use ($categoria, $competidoresOrganizacion) {
                    $competidor = $competidoresOrganizacion->get($persona->id);

                    if (! $competidor || $competidor->peso === null) {
                        return true;
                    }

                    return ! $this->pesoCumpleCategoria((float) $competidor->peso, $categoria);
                });

                if ($invalidPerson) {
                    return back()
                        ->withErrors(['persona_ids' => 'Todos los competidores deben tener un peso registrado que cumpla con la categoria seleccionada.'])
                        ->withInput();
                }
            }
        }

        $categoriasYaInscritas = InscripcionCompetidor::where('torneo_id', $torneo->id)
            ->whereIn('persona_id', $data['persona_ids'])
            ->whereHas('modalidades', function ($query) use ($data) {
                $query->whereIn('categoria_id', collect($data['modalidades'])->pluck('categoria_id'));
            })
            ->with(['persona', 'modalidades' => function ($query) use ($data) {
                $query->whereIn('categoria_id', collect($data['modalidades'])->pluck('categoria_id'));
            }, 'modalidades.categoria'])
            ->get();

        if ($categoriasYaInscritas->isNotEmpty()) {
            $names = $categoriasYaInscritas
                ->map(function ($competidor) {
                    return $competidor->persona->first_name;
                })
                ->filter()
                ->join(', ');

            return back()
                ->withErrors(['persona_ids' => 'Ya estan inscritos en la categoria seleccionada: ' . $names])
                ->withInput();
        }

        DB::transaction(function () use ($data, $torneo, $inscripcion) {
            foreach ($data['persona_ids'] as $personaId) {
                $competidor = InscripcionCompetidor::firstOrCreate(
                    [
                        'torneo_id' => $torneo->id,
                        'persona_id' => $personaId,
                    ],
                    [
                        'inscripcion_organizacion_id' => $inscripcion->id,
                    ]
                );

                foreach ($data['modalidades'] as $modalidad) {
                    $competidor->modalidades()->firstOrCreate(
                        [
                            'modalidad_id' => $modalidad['id'],
                            'categoria_id' => $modalidad['categoria_id'],
                        ],
                        [
                            'costo' => $modalidad['costo'],
                        ]
                    );
                }
            }
        });

        return redirect()
            ->route('inscripciones.participantes', [
                $torneo,
                $inscripcion,
                'modalidad_id' => $request->input('modalidad_filtro_id'),
                'categoria_id' => $request->input('categoria_filtro_id'),
            ])
            ->with('status', 'Participantes inscritos correctamente.');
    }

    public function updateParticipantePago(Request $request, Torneo $torneo, InscripcionOrganizacion $inscripcion, InscripcionCompetidor $competidor)
    {
        abort_unless($inscripcion->torneo_id === $torneo->id, 404);
        abort_unless($competidor->torneo_id === $torneo->id && $competidor->inscripcion_organizacion_id === $inscripcion->id, 404);

        $data = $request->validate([
            'costos' => ['required', 'array', 'min:1'],
            'costos.*' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        $detalles = $competidor->modalidades()
            ->whereIn('id', array_keys($data['costos']))
            ->get()
            ->keyBy('id');

        if ($detalles->count() !== count($data['costos'])) {
            return back()->withErrors(['costos' => 'No se pudo validar los montos del participante.']);
        }

        DB::transaction(function () use ($data, $detalles) {
            foreach ($data['costos'] as $detalleId => $costo) {
                $detalles->get((int) $detalleId)->update([
                    'costo' => $costo,
                ]);
            }
        });

        return back()->with('status', 'Monto del pago actualizado correctamente.');
    }

    public function destroyParticipante(Torneo $torneo, InscripcionOrganizacion $inscripcion, InscripcionCompetidor $competidor)
    {
        abort_unless($inscripcion->torneo_id === $torneo->id, 404);
        abort_unless($competidor->torneo_id === $torneo->id && $competidor->inscripcion_organizacion_id === $inscripcion->id, 404);

        $competidor->delete();

        return back()->with('status', 'Competidor eliminado de la inscripcion correctamente.');
    }

    private function categoriasQuery(Torneo $torneo, Request $request)
    {
        $categoriaIdsSorteadas = SorteoLlave::where('torneo_id', $torneo->id)
            ->pluck('categoria_id');

        return $torneo->categorias()
            ->with('modalidad')
            ->whereNotIn('categorias.id', $categoriaIdsSorteadas)
            ->when($request->filled('modalidad_id') && ! $request->filled('categoria_id'), function ($query) use ($request) {
                $query->where('modalidad_id', $request->input('modalidad_id'));
            })
            ->when($request->filled('categoria_id'), function ($query) use ($request) {
                $query->where('categorias.id', $request->input('categoria_id'));
            })
            ->leftJoin('modalidades', 'modalidades.id', '=', 'categorias.modalidad_id')
            ->orderBy('categorias.nombre')
            ->orderBy('modalidades.nombre')
            ->select('categorias.*');
    }

    private function categoriaRequierePeso($categoria): bool
    {
        $modalidadNombre = mb_strtolower((string) optional($categoria->modalidad)->nombre);

        return str_contains($modalidadNombre, 'kumite') && $categoria->peso_hasta !== null;
    }

    private function categoriaPesoEsMinimo($categoria): bool
    {
        $nombre = mb_strtolower((string) $categoria->nombre);

        return str_contains($nombre, 'mayor o igual') || str_contains($nombre, '>=');
    }

    private function categoriaTieneEdadExacta($categoria): bool
    {
        if ($categoria->edad_desde === null) {
            return false;
        }

        return $categoria->edad_hasta === null
            || (int) $categoria->edad_desde === (int) $categoria->edad_hasta;
    }

    private function pesoCumpleCategoria(float $peso, $categoria): bool
    {
        if ($this->categoriaPesoEsMinimo($categoria)) {
            return $peso >= (float) $categoria->peso_hasta;
        }

        return $peso <= (float) $categoria->peso_hasta;
    }
}
