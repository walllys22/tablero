<?php

namespace App\Http\Controllers;

use App\Models\InscripcionCompetidor;
use App\Models\InscripcionOrganizacion;
use App\Models\Organizacion;
use App\Models\Persona;
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

        return view('inscripciones.browse', compact('torneo', 'personas', 'organizaciones', 'categorias', 'organizacionesInscritas'));
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

        return view('inscripciones.list', compact('organizaciones', 'torneo'));
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
        $generosDisponibles = $categoriasDisponibles
            ->pluck('genero')
            ->filter()
            ->unique()
            ->values();
        $categoriaIdsDisponibles = $categoriasDisponibles->pluck('id');
        $personasNoDisponibles = collect();

        if ($categoriaIdsDisponibles->isNotEmpty()) {
            $personasNoDisponibles = InscripcionCompetidor::where('torneo_id', $torneo->id)
                ->whereHas('modalidades', function ($query) use ($categoriaIdsDisponibles) {
                    $query->whereIn('categoria_id', $categoriaIdsDisponibles);
                })
                ->pluck('persona_id');
        }

        $personas = Persona::where('status', 1)
            ->when($generosDisponibles->isNotEmpty() && ! $generosDisponibles->contains('Mixto'), function ($query) use ($generosDisponibles) {
                $query->whereIn('gender', $generosDisponibles);
            })
            ->when($personasNoDisponibles->isNotEmpty(), function ($query) use ($personasNoDisponibles) {
                $query->whereNotIn('id', $personasNoDisponibles);
            })
            ->orderBy('first_name')
            ->get();
        $categorias = $torneo->categorias()
            ->with('modalidad')
            ->when($request->filled('modalidad_id'), function ($query) use ($request) {
                $query->where('modalidad_id', $request->input('modalidad_id'));
            })
            ->orderBy('nombre')
            ->get();
        $modalidades = $torneo->modalidades()->orderBy('nombre')->get();
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
            'competidores'
        ));
    }

    public function storeParticipante(Request $request, Torneo $torneo, InscripcionOrganizacion $inscripcion)
    {
        abort_unless($inscripcion->torneo_id === $torneo->id, 404);

        $data = $request->validate([
            'persona_ids' => ['required', 'array', 'min:1'],
            'persona_ids.*' => ['required', 'distinct', Rule::exists('personas', 'id')],
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

        foreach ($data['modalidades'] as $modalidad) {
            $categoria = $categoriasSeleccionadas->get($modalidad['categoria_id']);

            if (! $categoria || (int) $categoria->modalidad_id !== (int) $modalidad['id']) {
                return back()
                    ->withErrors(['modalidades' => 'La categoria seleccionada no corresponde a la modalidad.'])
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
                    $competidor->modalidades()->create([
                        'modalidad_id' => $modalidad['id'],
                        'categoria_id' => $modalidad['categoria_id'],
                        'costo' => $modalidad['costo'],
                    ]);
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

    private function categoriasQuery(Torneo $torneo, Request $request)
    {
        return $torneo->categorias()
            ->with('modalidad')
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
}
