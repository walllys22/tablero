<?php

namespace App\Http\Controllers;

use App\Models\InscripcionCompetidorModalidad;
use App\Models\SorteoLlave;
use App\Models\Torneo;
use Illuminate\Http\Request;

class SorteoLlaveController extends Controller
{
    public function index(Request $request, Torneo $torneo)
    {
        [$modalidades, $categoriaIdsConCompetidores] = $this->modalidadesDisponiblesParaSorteo($torneo);
        $categoriaIdsSorteadas = SorteoLlave::where('torneo_id', $torneo->id)
            ->pluck('categoria_id')
            ->map(fn ($id) => (int) $id);

        $categoria = null;
        $competidores = collect();
        $llaves = [];
        $seed = (int) $request->input('seed', random_int(100000, 999999));
        $sistemaSorteo = $request->input('sistema_sorteo') === 'round_robin'
            ? 'round_robin'
            : 'eliminacion_directa';
        $sorteos = SorteoLlave::with(['modalidad', 'categoria', 'resultadosKumite'])
            ->withCount('resultadosKumite')
            ->where('torneo_id', $torneo->id)
            ->orderByRaw('COALESCE(orden, 999999)')
            ->orderBy('id')
            ->get();
        $this->asignarAreaUnica($torneo, $sorteos);
        $this->prepararLlavesSorteos($sorteos);
        $sorteoActual = null;

        if ($request->filled('categoria_id')) {
            $categoria = $torneo->categorias()
                ->with('modalidad')
                ->where('modalidad_id', $request->input('modalidad_id'))
                ->whereIn('id', $categoriaIdsConCompetidores)
                ->find($request->input('categoria_id'));

            if ($categoria) {
                $competidores = $this->competidoresCategoria($torneo, (int) $categoria->id);
                $sorteoActual = SorteoLlave::where('torneo_id', $torneo->id)
                    ->where('categoria_id', $categoria->id)
                    ->first();

                if ($sorteoActual && ! $request->boolean('sortear')) {
                    $seed = $sorteoActual->seed;
                    $sorteoActual->loadMissing('resultadosKumite');
                    $llaves = $this->llavesConPases($sorteoActual->llaves, $sorteoActual);
                }

                if ($request->boolean('sortear') && ! $sorteoActual && $competidores->count() >= 2) {
                    $llaves = $sistemaSorteo === 'round_robin'
                        ? $this->crearLlavesRoundRobin($competidores, $seed)
                        : $this->crearLlaves($competidores, $seed);
                    $sorteoActual = SorteoLlave::create([
                        'torneo_id' => $torneo->id,
                        'modalidad_id' => $categoria->modalidad_id,
                        'categoria_id' => $categoria->id,
                        'seed' => $seed,
                        'llaves' => $llaves,
                        'area' => ((int) ($torneo->cantidad_areas ?? 1)) === 1 ? 1 : null,
                        'orden' => $this->siguienteOrden($torneo),
                    ]);
                    $sorteos = SorteoLlave::with(['modalidad', 'categoria', 'resultadosKumite'])
                        ->withCount('resultadosKumite')
                        ->where('torneo_id', $torneo->id)
                        ->orderByRaw('COALESCE(orden, 999999)')
                        ->orderBy('id')
                        ->get();
                    $this->asignarAreaUnica($torneo, $sorteos);
                    $this->prepararLlavesSorteos($sorteos);

                    if ($request->ajax()) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Categoria sorteada correctamente.',
                        ]);
                    }

                    return redirect()
                        ->route('sorteo-llaves.index', $torneo)
                        ->with('status', 'Categoria sorteada correctamente.');
                }
            }
        }

        return view('sorteo_llaves.browse', compact('torneo', 'modalidades', 'categoria', 'competidores', 'llaves', 'seed', 'sorteos', 'sorteoActual', 'categoriaIdsSorteadas'));
    }

    public function categoriasDisponibles(Torneo $torneo)
    {
        [$modalidades] = $this->modalidadesDisponiblesParaSorteo($torneo);
        $categoriaIdsSorteadas = SorteoLlave::where('torneo_id', $torneo->id)
            ->pluck('categoria_id')
            ->map(fn ($id) => (int) $id);

        return response()->json([
            'modalidades' => $modalidades->map(function ($modalidad) use ($categoriaIdsSorteadas) {
                return [
                    'id' => $modalidad->id,
                    'nombre' => $modalidad->nombre,
                    'categorias' => $modalidad->categorias->map(function ($categoria) use ($categoriaIdsSorteadas) {
                        return [
                            'id' => $categoria->id,
                            'nombre' => $categoria->nombre,
                            'modalidad_id' => $categoria->modalidad_id,
                            'sorteada' => $categoriaIdsSorteadas->contains((int) $categoria->id),
                        ];
                    })->values(),
                ];
            })->values(),
        ]);
    }

    public function graphic(Request $request, Torneo $torneo)
    {
        $request->validate([
            'modalidad_id' => ['required'],
            'categoria_id' => ['required'],
            'seed' => ['required', 'integer'],
        ]);

        $sorteo = SorteoLlave::with(['modalidad', 'categoria.modalidad', 'resultadosKumite'])
            ->where('torneo_id', $torneo->id)
            ->where('categoria_id', $request->input('categoria_id'))
            ->first();

        $categoria = $sorteo
            ? $sorteo->categoria
            : $torneo->categorias()
                ->with('modalidad')
                ->where('modalidad_id', $request->input('modalidad_id'))
                ->findOrFail($request->input('categoria_id'));

        $competidores = $this->competidoresCategoria($torneo, (int) $categoria->id);
        $llaves = $sorteo
            ? $sorteo->llaves
            : ($competidores->count() >= 2 ? $this->crearLlaves($competidores, (int) $request->input('seed')) : []);
        $llaves = $sorteo ? $this->llavesConPases($llaves, $sorteo) : $llaves;

        return view('sorteo_llaves.graphic', compact('torneo', 'categoria', 'competidores', 'llaves', 'sorteo'));
    }

    public function resultados(Torneo $torneo, SorteoLlave $sorteo)
    {
        abort_unless((int) $sorteo->torneo_id === (int) $torneo->id, 404);

        $sorteo->load([
            'modalidad',
            'categoria',
            'resultadosKumite' => function ($query) {
                $query->orderBy('numero_llave');
            },
        ]);

        return view('sorteo_llaves.resultados', compact('torneo', 'sorteo'));
    }

    public function updateOrden(Request $request, Torneo $torneo)
    {
        $data = $request->validate([
            'orden' => ['required', 'array', 'min:1'],
            'orden.*' => ['required', 'integer', 'distinct'],
        ]);

        $sorteos = SorteoLlave::withCount('resultadosKumite')
            ->where('torneo_id', $torneo->id)
            ->orderByRaw('COALESCE(orden, 999999)')
            ->orderBy('id')
            ->get()
            ->keyBy('id');
        $ids = collect($data['orden'])->map(fn ($id) => (int) $id)->values();

        abort_unless($ids->count() === $sorteos->count() && $ids->diff($sorteos->keys())->isEmpty(), 422);

        $ordenActual = $sorteos->keys()->values();
        $bloqueados = $sorteos->filter(fn ($sorteo) => (int) $sorteo->resultados_kumite_count > 0);

        foreach ($bloqueados as $sorteo) {
            if ($ordenActual->search($sorteo->id) !== $ids->search($sorteo->id)) {
                return back()->withErrors([
                    'orden' => 'No se puede reordenar una categoria que tiene combates realizados o en curso.',
                ]);
            }
        }

        foreach ($ids as $index => $id) {
            $sorteo = $sorteos->get($id);

            if ((int) $sorteo->resultados_kumite_count > 0) {
                continue;
            }

            $sorteo->update([
                'orden' => $index + 1,
            ]);
        }

        return redirect()
            ->route('sorteo-llaves.index', $torneo)
            ->with('status', 'Orden de categorias actualizado correctamente.');
    }

    public function updateArea(Request $request, Torneo $torneo, SorteoLlave $sorteo)
    {
        abort_unless((int) $sorteo->torneo_id === (int) $torneo->id, 404);

        $data = $request->validate([
            'area' => ['required', 'integer', 'min:1', 'max:' . max(1, (int) $torneo->cantidad_areas)],
        ]);

        $sorteo->update([
            'area' => $data['area'],
        ]);

        return redirect()
            ->route('sorteo-llaves.index', $torneo)
            ->with('status', 'Area asignada correctamente.');
    }

    public function destroy(Torneo $torneo, SorteoLlave $sorteo)
    {
        abort_unless((int) $sorteo->torneo_id === (int) $torneo->id, 404);

        $sorteo->delete();

        return redirect()
            ->route('sorteo-llaves.index', $torneo)
            ->with('status', 'Sorteo eliminado correctamente. Puede sortear la categoria nuevamente.');
    }

    private function competidoresCategoria(Torneo $torneo, int $categoriaId)
    {
        return InscripcionCompetidorModalidad::query()
            ->with([
                'inscripcionCompetidor.persona',
                'inscripcionCompetidor.inscripcionOrganizacion.organizacion',
            ])
            ->where('categoria_id', $categoriaId)
            ->whereHas('inscripcionCompetidor', function ($query) use ($torneo) {
                $query->where('torneo_id', $torneo->id);
            })
            ->get()
            ->map(function ($detalle) {
                $competidor = $detalle->inscripcionCompetidor;
                $organizacion = $competidor->inscripcionOrganizacion->organizacion;

                return [
                    'id' => $competidor->id,
                    'nombre' => $competidor->persona->first_name ?? 'Sin nombre',
                    'organizacion_id' => $organizacion->id ?? null,
                    'organizacion' => $organizacion->nombre ?? 'Sin organizacion',
                ];
            })
            ->unique('id')
            ->values();
    }

    private function llavesConPases(array $llaves, SorteoLlave $sorteo): array
    {
        $llaves = $this->propagarByes($this->reiniciarLlavesBase($llaves));

        foreach ($sorteo->resultadosKumite->sortBy('indice_combate') as $resultado) {
            $position = $this->posicionCombatePorIndice($llaves, (int) $resultado->indice_combate);

            if (! $position) {
                continue;
            }

            [$roundIndex, $matchIndex] = $position;
            $combate = $llaves[$roundIndex]['combates'][$matchIndex];
            $ganadorSide = $resultado->ganador_color === 'rojo' ? 'a' : 'b';
            $ganadorCompetidor = $combate[$ganadorSide] ?? [
                'nombre' => $resultado->ganador,
                'organizacion' => '',
                'organizacion_id' => null,
            ];

            $nextRound = $roundIndex + 1;
            $nextMatch = (int) floor($matchIndex / 2);
            $nextSide = $matchIndex % 2 === 0 ? 'a' : 'b';

            if (isset($llaves[$nextRound]['combates'][$nextMatch])) {
                $llaves[$nextRound]['combates'][$nextMatch][$nextSide] = $ganadorCompetidor;
            }
        }

        return $llaves;
    }

    private function prepararLlavesSorteos($sorteos): void
    {
        $sorteos->each(function ($sorteo) {
            $llaves = $this->llavesConPases($sorteo->llaves, $sorteo);
            $categoriaCompleta = $this->categoriaCompleta($llaves, $sorteo);

            $sorteo->setAttribute('llaves', $llaves);
            $sorteo->setAttribute('categoria_completa', $categoriaCompleta);
            $sorteo->setAttribute('categoria_estado', $this->estadoCategoria($sorteo, $categoriaCompleta));
            $sorteo->setAttribute('podio_modal', $this->podioSorteo($llaves, $sorteo));
        });
    }

    private function estadoCategoria(SorteoLlave $sorteo, bool $categoriaCompleta): string
    {
        if ($categoriaCompleta) {
            return 'realizada';
        }

        return $sorteo->resultadosKumite->isNotEmpty() ? 'ejecucion' : 'pendiente';
    }

    private function categoriaCompleta(array $llaves, SorteoLlave $sorteo): bool
    {
        $resultadosPorIndice = $sorteo->resultadosKumite->keyBy('indice_combate');
        $indice = 0;
        $combatesNecesarios = 0;

        foreach ($llaves as $ronda) {
            foreach (($ronda['combates'] ?? []) as $combate) {
                $rojo = trim((string) ($combate['a']['nombre'] ?? ''));
                $azul = trim((string) ($combate['b']['nombre'] ?? ''));
                $requiereResultado = ! ($combate['bye'] ?? false)
                    && $rojo !== ''
                    && $azul !== ''
                    && strtoupper($rojo) !== 'BYE'
                    && strtoupper($azul) !== 'BYE';

                if ($requiereResultado) {
                    $combatesNecesarios++;

                    if (! $resultadosPorIndice->has($indice)) {
                        return false;
                    }
                }

                $indice++;
            }
        }

        return $combatesNecesarios > 0;
    }

    private function podioSorteo(array $llaves, SorteoLlave $sorteo): array
    {
        $podio = [
            'oro' => '',
            'plata' => '',
            'bronce_1' => '',
            'bronce_2' => '',
        ];

        if (empty($llaves)) {
            return $podio;
        }

        $resultadosPorIndice = $sorteo->resultadosKumite->keyBy('indice_combate');
        $finalRound = count($llaves) - 1;
        $finalIndex = $this->indiceCombatePorPosicion($llaves, $finalRound, 0);
        $finalResultado = $finalIndex !== null ? $resultadosPorIndice->get($finalIndex) : null;

        if ($finalResultado && isset($llaves[$finalRound]['combates'][0])) {
            $final = $llaves[$finalRound]['combates'][0];
            $rojo = $this->nombreCompetidorPodio($final['a'] ?? null);
            $azul = $this->nombreCompetidorPodio($final['b'] ?? null);

            if ($finalResultado->ganador_color === 'rojo') {
                $podio['oro'] = $rojo;
                $podio['plata'] = $azul;
            } else {
                $podio['oro'] = $azul;
                $podio['plata'] = $rojo;
            }
        }

        if (count($llaves) < 2) {
            return $podio;
        }

        $semifinalRound = count($llaves) - 2;
        $bronces = [];

        foreach (($llaves[$semifinalRound]['combates'] ?? []) as $matchIndex => $combate) {
            $indice = $this->indiceCombatePorPosicion($llaves, $semifinalRound, $matchIndex);
            $resultado = $indice !== null ? $resultadosPorIndice->get($indice) : null;

            if (! $resultado) {
                continue;
            }

            $rojo = $this->nombreCompetidorPodio($combate['a'] ?? null);
            $azul = $this->nombreCompetidorPodio($combate['b'] ?? null);
            $perdedor = $resultado->ganador_color === 'rojo' ? $azul : $rojo;

            if ($perdedor) {
                $bronces[] = $perdedor;
            }
        }

        $podio['bronce_1'] = $bronces[0] ?? '';
        $podio['bronce_2'] = $bronces[1] ?? '';

        return $podio;
    }

    private function nombreCompetidorPodio(?array $competidor): string
    {
        $nombre = $competidor['nombre'] ?? '';

        return in_array($nombre, ['', 'BYE', 'Competidor', 'Pendiente'], true) || str_starts_with($nombre, 'Ganador')
            ? ''
            : $nombre;
    }

    private function reiniciarLlavesBase(array $llaves): array
    {
        foreach ($llaves as $roundIndex => $ronda) {
            foreach (($ronda['combates'] ?? []) as $matchIndex => $combate) {
                unset(
                    $llaves[$roundIndex]['combates'][$matchIndex]['realizado'],
                    $llaves[$roundIndex]['combates'][$matchIndex]['ganador']
                );

                if ($roundIndex > 0) {
                    unset(
                        $llaves[$roundIndex]['combates'][$matchIndex]['a'],
                        $llaves[$roundIndex]['combates'][$matchIndex]['b']
                    );
                }
            }
        }

        return $llaves;
    }

    private function propagarByes(array $llaves): array
    {
        foreach ($llaves as $roundIndex => $ronda) {
            if (! isset($llaves[$roundIndex + 1])) {
                continue;
            }

            foreach (($ronda['combates'] ?? []) as $matchIndex => $combate) {
                if (! ($combate['bye'] ?? false)) {
                    continue;
                }

                $ganador = $combate['a'] ?? $combate['b'] ?? null;

                if (! $ganador) {
                    continue;
                }

                $nextMatch = (int) floor($matchIndex / 2);
                $nextSide = $matchIndex % 2 === 0 ? 'a' : 'b';

                if (isset($llaves[$roundIndex + 1]['combates'][$nextMatch])) {
                    $llaves[$roundIndex + 1]['combates'][$nextMatch][$nextSide] = $ganador;
                }
            }
        }

        return $llaves;
    }

    private function posicionCombatePorIndice(array $llaves, int $indice): ?array
    {
        $actual = 0;

        foreach ($llaves as $roundIndex => $ronda) {
            foreach (($ronda['combates'] ?? []) as $matchIndex => $combate) {
                if ($actual === $indice) {
                    return [$roundIndex, $matchIndex];
                }

                $actual++;
            }
        }

        return null;
    }

    private function indiceCombatePorPosicion(array $llaves, int $roundIndex, int $matchIndex): ?int
    {
        $indice = 0;

        foreach ($llaves as $rondaIndex => $ronda) {
            foreach (($ronda['combates'] ?? []) as $combateIndex => $combate) {
                if ($rondaIndex === $roundIndex && $combateIndex === $matchIndex) {
                    return $indice;
                }

                $indice++;
            }
        }

        return null;
    }

    private function asignarAreaUnica(Torneo $torneo, $sorteos): void
    {
        if ((int) ($torneo->cantidad_areas ?? 1) !== 1) {
            return;
        }

        $sorteosSinArea = $sorteos->filter(fn ($sorteo) => ! $sorteo->area);

        if ($sorteosSinArea->isEmpty()) {
            return;
        }

        SorteoLlave::whereIn('id', $sorteosSinArea->pluck('id'))->update(['area' => 1]);

        $sorteosSinArea->each(function ($sorteo) {
            $sorteo->area = 1;
        });
    }

    private function siguienteOrden(Torneo $torneo): int
    {
        $sorteos = SorteoLlave::where('torneo_id', $torneo->id);

        return max((int) $sorteos->max('orden'), (int) $sorteos->count()) + 1;
    }

    private function modalidadesDisponiblesParaSorteo(Torneo $torneo): array
    {
        $categoriaIdsConCompetidores = InscripcionCompetidorModalidad::query()
            ->whereHas('inscripcionCompetidor', function ($query) use ($torneo) {
                $query->where('torneo_id', $torneo->id);
            })
            ->select('categoria_id')
            ->groupBy('categoria_id')
            ->havingRaw('COUNT(DISTINCT inscripcion_competidor_id) >= 2')
            ->pluck('categoria_id');

        $modalidades = $torneo->modalidades()
            ->whereHas('categorias', function ($query) use ($categoriaIdsConCompetidores) {
                $query->whereIn('categorias.id', $categoriaIdsConCompetidores);
            })
            ->with(['categorias' => function ($query) use ($categoriaIdsConCompetidores) {
                $query->whereIn('categorias.id', $categoriaIdsConCompetidores)
                    ->orderBy('edad_desde')
                    ->orderBy('edad_hasta')
                    ->orderBy('genero')
                    ->orderBy('peso_hasta')
                    ->orderBy('nombre');
            }])
            ->orderBy('nombre')
            ->get();

        return [$modalidades, $categoriaIdsConCompetidores];
    }

    private function crearLlaves($competidores, int $seed): array
    {
        $total = $competidores->count();

        if ($total === 0) {
            return [];
        }

        $tamanoLlave = $this->siguientePotenciaDos($total);
        $byes = $tamanoLlave - $total;
        $pendientes = $this->ordenarSorteo($competidores, $seed, 'competidores');
        $primeraRonda = collect();

        for ($i = 0; $i < $byes; $i++) {
            $primeraRonda->push([
                'a' => $pendientes->shift(),
                'b' => null,
                'bye' => true,
            ]);
        }

        while ($pendientes->isNotEmpty()) {
            $a = $pendientes->shift();
            $opponentIndex = $pendientes->search(function ($competidor) use ($a) {
                return $competidor['organizacion_id'] !== $a['organizacion_id'];
            });

            if ($opponentIndex === false) {
                $opponentIndex = 0;
            }

            $b = $pendientes->pull($opponentIndex);
            $pendientes = $pendientes->values();

            $primeraRonda->push([
                'a' => $a,
                'b' => $b,
                'bye' => false,
            ]);
        }

        $rondas = [[
            'nombre' => $this->nombreRonda($tamanoLlave),
            'sistema' => 'eliminacion_directa',
            'combates' => $this->ordenarSorteo($primeraRonda, $seed, 'combates'),
        ]];

        $combates = (int) ($tamanoLlave / 4);
        while ($combates >= 1) {
            $rondas[] = [
                'nombre' => $combates === 1 ? 'Final' : $this->nombreRonda($combates * 2),
                'sistema' => 'eliminacion_directa',
                'combates' => collect(range(1, $combates))->map(function () {
                    return [
                        'a' => null,
                        'b' => null,
                        'bye' => false,
                    ];
                }),
            ];

            $combates = (int) floor($combates / 2);
        }

        return $rondas;
    }

    private function crearLlavesRoundRobin($competidores, int $seed): array
    {
        $participantes = $this->ordenarSorteo($competidores, $seed, 'round-robin-competidores');
        $combates = collect();

        for ($i = 0; $i < $participantes->count(); $i++) {
            for ($j = $i + 1; $j < $participantes->count(); $j++) {
                $combates->push([
                    'a' => $participantes[$i],
                    'b' => $participantes[$j],
                    'bye' => false,
                    'round_robin' => true,
                ]);
            }
        }

        return [[
            'nombre' => 'Round Robin',
            'sistema' => 'round_robin',
            'combates' => $this->ordenarSorteo($combates, $seed, 'round-robin-combates'),
        ]];
    }

    private function ordenarSorteo($items, int $seed, string $salt)
    {
        return $items
            ->sortBy(function ($item, $index) use ($seed, $salt) {
                $id = is_array($item) && isset($item['id'])
                    ? $item['id']
                    : ($item['a']['id'] ?? $index);

                return sprintf('%u', crc32($seed . '-' . $salt . '-' . $id . '-' . $index));
            })
            ->values();
    }

    private function siguientePotenciaDos(int $number): int
    {
        $power = 1;

        while ($power < $number) {
            $power *= 2;
        }

        return $power;
    }

    private function nombreRonda(int $tamano): string
    {
        return match ($tamano) {
            2 => 'Final',
            4 => 'Semifinal',
            8 => 'Cuartos de final',
            16 => 'Octavos de final',
            32 => 'Dieciseisavos de final',
            default => 'Ronda de ' . $tamano,
        };
    }
}
