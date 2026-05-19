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
        $sorteos = SorteoLlave::with(['modalidad', 'categoria', 'resultadosKumite', 'resultadosKata'])
            ->withCount(['resultadosKumite', 'resultadosKata'])
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
                    $sorteoActual->loadMissing(['modalidad', 'resultadosKumite', 'resultadosKata']);
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
                    $sorteos = SorteoLlave::with(['modalidad', 'categoria', 'resultadosKumite', 'resultadosKata'])
                        ->withCount(['resultadosKumite', 'resultadosKata'])
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

                if ($request->ajax() && $request->boolean('sortear')) {
                    return response()->json([
                        'success' => false,
                        'message' => $sorteoActual
                            ? 'Esta categoria ya fue sorteada.'
                            : 'Se necesitan al menos 2 competidores para sortear esta categoria.',
                    ], 422);
                }
            }

            if ($request->ajax() && $request->boolean('sortear')) {
                return response()->json([
                    'success' => false,
                    'message' => 'La categoria seleccionada no esta disponible para sortear.',
                ], 422);
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

        $sorteo = SorteoLlave::with(['modalidad', 'categoria.modalidad', 'resultadosKumite', 'resultadosKata'])
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

        $sorteo->loadMissing('modalidad');

        if ($this->esSorteoKata($sorteo)) {
            $sorteo->load([
                'modalidad',
                'categoria',
                'resultadosKata' => function ($query) {
                    $query->orderBy('indice_combate');
                },
            ]);

            return view('sorteo_llaves.resultados_kata', compact('torneo', 'sorteo'));
        }

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

        $sorteos = SorteoLlave::withCount(['resultadosKumite', 'resultadosKata'])
            ->with(['modalidad', 'resultadosKumite', 'resultadosKata'])
            ->where('torneo_id', $torneo->id)
            ->orderByRaw('COALESCE(orden, 999999)')
            ->orderBy('id')
            ->get();
        $this->prepararLlavesSorteos($sorteos);
        $sorteos = $sorteos->keyBy('id');
        $ids = collect($data['orden'])->map(fn ($id) => (int) $id)->values();

        abort_unless($ids->count() === $sorteos->count() && $ids->diff($sorteos->keys())->isEmpty(), 422);

        $ordenActual = $sorteos->keys()->values();
        $bloqueados = $sorteos->filter(fn ($sorteo) => ($sorteo->categoria_estado ?? 'pendiente') !== 'pendiente');

        foreach ($bloqueados as $sorteo) {
            if ($ordenActual->search($sorteo->id) !== $ids->search($sorteo->id)) {
                return back()->withErrors([
                    'orden' => 'No se puede reordenar una categoria en ejecucion o realizada.',
                ]);
            }
        }

        foreach ($ids as $index => $id) {
            $sorteo = $sorteos->get($id);

            if (($sorteo->categoria_estado ?? 'pendiente') !== 'pendiente') {
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

    public function updateCompetidores(Request $request, Torneo $torneo, SorteoLlave $sorteo)
    {
        abort_unless((int) $sorteo->torneo_id === (int) $torneo->id, 404);

        $data = $request->validate([
            'slots' => ['required', 'array', 'min:1'],
            'slots.*.round_index' => ['nullable', 'integer', 'min:0'],
            'slots.*.match_index' => ['required', 'integer', 'min:0'],
            'slots.*.side' => ['required', 'in:a,b'],
            'slots.*.competidor_id' => ['nullable', 'integer'],
        ]);

        $sorteo->loadMissing(['modalidad', 'resultadosKumite', 'resultadosKata']);
        $llaves = $sorteo->llaves ?? [];

        $resultadosPorIndice = $this->resultadosCompetencia($sorteo)->keyBy('indice_combate');

        if (($llaves[0]['sistema'] ?? null) === 'round_robin') {
            return response()->json([
                'message' => 'El movimiento manual solo esta disponible para eliminacion directa.',
            ], 422);
        }

        if (! isset($llaves[0]['combates'])) {
            return response()->json([
                'message' => 'No hay llaves disponibles para actualizar.',
            ], 422);
        }

        $competidores = $this->competidoresCategoria($torneo, (int) $sorteo->categoria_id)->keyBy('id');
        $slots = collect($data['slots']);
        $slotKeys = $slots->map(fn ($slot) => ($slot['round_index'] ?? 0) . ':' . $slot['match_index'] . ':' . $slot['side']);
        $tocaRondasPosteriores = $slots->contains(fn ($slot) => (int) ($slot['round_index'] ?? 0) > 0);

        if ($slotKeys->duplicates()->isNotEmpty()) {
            return response()->json([
                'message' => 'La distribucion enviada contiene posiciones repetidas.',
            ], 422);
        }

        $competidorIds = $slots
            ->pluck('competidor_id')
            ->filter(fn ($id) => $id !== null)
            ->map(fn ($id) => (int) $id)
            ->values();

        if (
            $competidorIds->diff($competidores->keys())->isNotEmpty()
            || (! $tocaRondasPosteriores && $competidorIds->duplicates()->isNotEmpty())
        ) {
            return response()->json([
                'message' => 'La distribucion enviada contiene competidores invalidos.',
            ], 422);
        }

        foreach ($slots->groupBy(fn ($slot) => (int) ($slot['round_index'] ?? 0)) as $roundSlots) {
            $idsPorRonda = $roundSlots
                ->pluck('competidor_id')
                ->filter(fn ($id) => $id !== null)
                ->map(fn ($id) => (int) $id)
                ->values();

            if ($idsPorRonda->duplicates()->isNotEmpty()) {
                return response()->json([
                    'message' => 'La distribucion enviada contiene competidores repetidos en una misma ronda.',
                ], 422);
            }
        }

        foreach ($slots as $slot) {
            $roundIndex = (int) ($slot['round_index'] ?? 0);
            $matchIndex = (int) $slot['match_index'];
            $side = $slot['side'];

            if (! isset($llaves[$roundIndex]['combates'][$matchIndex])) {
                return response()->json([
                    'message' => 'La distribucion enviada contiene posiciones invalidas.',
                ], 422);
            }

            $indiceCombate = $this->indiceCombatePorPosicion($llaves, $roundIndex, $matchIndex);

            if ($indiceCombate !== null && $resultadosPorIndice->has($indiceCombate)) {
                return response()->json([
                    'message' => 'No se puede cambiar una llave que ya fue realizada.',
                ], 422);
            }

            $competidorId = ($slot['competidor_id'] ?? null) !== null ? (int) $slot['competidor_id'] : null;
            $llaves[$roundIndex]['combates'][$matchIndex][$side] = $competidorId ? $competidores->get($competidorId) : null;
        }

        if (! $tocaRondasPosteriores) {
            $idsActuales = collect($llaves[0]['combates'])
                ->flatMap(fn ($combate) => [
                    $combate['a']['id'] ?? null,
                    $combate['b']['id'] ?? null,
                ])
                ->filter(fn ($id) => $id !== null)
                ->map(fn ($id) => (int) $id)
                ->values();
            $competidoresFaltantes = $competidores->keys()
                ->map(fn ($id) => (int) $id)
                ->diff($idsActuales);

            foreach ($competidoresFaltantes as $competidorFaltanteId) {
                foreach ($llaves[0]['combates'] as $matchIndex => $combate) {
                    foreach (['a', 'b'] as $side) {
                        if (! empty($llaves[0]['combates'][$matchIndex][$side])) {
                            continue;
                        }

                        $llaves[0]['combates'][$matchIndex][$side] = $competidores->get($competidorFaltanteId);
                        continue 3;
                    }
                }
            }

            $idsDistribuidos = collect($llaves[0]['combates'])
                ->flatMap(fn ($combate) => [
                    $combate['a']['id'] ?? null,
                    $combate['b']['id'] ?? null,
                ])
                ->filter(fn ($id) => $id !== null)
                ->map(fn ($id) => (int) $id)
                ->values();

            if (
                $idsDistribuidos->duplicates()->isNotEmpty()
                || $idsDistribuidos->sort()->values()->all() !== $competidores->keys()->sort()->values()->all()
            ) {
                return response()->json([
                    'message' => 'La distribucion debe mantener todos los competidores una sola vez.',
                ], 422);
            }
        }

        foreach ($slots->groupBy(fn ($slot) => (int) ($slot['round_index'] ?? 0)) as $roundIndex => $roundSlots) {
            foreach ($roundSlots->pluck('match_index')->unique() as $matchIndex) {
                if (! isset($llaves[$roundIndex]['combates'][$matchIndex])) {
                    continue;
                }

                $tieneRojo = ! empty($llaves[$roundIndex]['combates'][$matchIndex]['a']);
                $tieneAzul = ! empty($llaves[$roundIndex]['combates'][$matchIndex]['b']);
                $llaves[$roundIndex]['combates'][$matchIndex]['bye'] = $tieneRojo !== $tieneAzul;
            }
        }

        if (! $tocaRondasPosteriores) {
            for ($roundIndex = 1; $roundIndex < count($llaves); $roundIndex++) {
                foreach (($llaves[$roundIndex]['combates'] ?? []) as $matchIndex => $combate) {
                    $llaves[$roundIndex]['combates'][$matchIndex]['a'] = null;
                    $llaves[$roundIndex]['combates'][$matchIndex]['b'] = null;
                    $llaves[$roundIndex]['combates'][$matchIndex]['bye'] = false;
                }
            }
        }

        $sorteo->update(['llaves' => $llaves]);

        return response()->json([
            'success' => true,
            'message' => 'Competidores actualizados correctamente.',
        ]);
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

        $sorteo->loadMissing(['modalidad', 'resultadosKumite', 'resultadosKata']);
        $llaves = $this->llavesConPases($sorteo->llaves, $sorteo);
        $categoriaCompleta = $this->categoriaCompleta($llaves, $sorteo);

        if ($this->estadoCategoria($sorteo, $categoriaCompleta) !== 'pendiente') {
            return redirect()
                ->route('sorteo-llaves.index', $torneo)
                ->withErrors(['delete' => 'Solo se puede eliminar una categoria pendiente.']);
        }

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

    private function esSorteoKata(SorteoLlave $sorteo): bool
    {
        return str_contains(mb_strtolower($sorteo->modalidad->nombre ?? ''), 'kata');
    }

    private function resultadosCompetencia(SorteoLlave $sorteo)
    {
        return $this->esSorteoKata($sorteo)
            ? $sorteo->resultadosKata
            : $sorteo->resultadosKumite;
    }

    private function llavesConPases(array $llaves, SorteoLlave $sorteo): array
    {
        $llaves = $this->propagarByes($this->reiniciarLlavesBase($llaves));

        foreach ($this->resultadosCompetencia($sorteo)->sortBy('indice_combate') as $resultado) {
            $position = $this->posicionCombatePorIndice($llaves, (int) $resultado->indice_combate);

            if (! $position) {
                continue;
            }

            [$roundIndex, $matchIndex] = $position;
            $combate = $llaves[$roundIndex]['combates'][$matchIndex];
            $ganadorSide = $this->esSorteoKata($sorteo)
                ? $this->ladoGanadorKata($resultado, $combate)
                : ($resultado->ganador_color === 'rojo' ? 'a' : 'b');
            $ganadorCompetidor = $combate[$ganadorSide] ?? [
                'nombre' => $resultado->ganador,
                'organizacion' => '',
                'organizacion_id' => null,
            ];

            $nextRound = $roundIndex + 1;
            $nextMatch = (int) floor($matchIndex / 2);
            $nextSide = $matchIndex % 2 === 0 ? 'a' : 'b';

            if (isset($llaves[$nextRound]['combates'][$nextMatch])
                && $this->puedePropagarGanadorA($llaves[$nextRound]['combates'][$nextMatch][$nextSide] ?? null)) {
                $llaves[$nextRound]['combates'][$nextMatch][$nextSide] = $ganadorCompetidor;
            }

            $llaves = $this->propagarByes($llaves);
        }

        return $llaves;
    }

    private function ladoGanadorKata($resultado, array $combate): string
    {
        $ganador = $this->normalizarNombreCompetidor($resultado->ganador ?? null);
        $rojo = $this->normalizarNombreCompetidor(($combate['a']['nombre'] ?? null) ?: ($resultado->competidor_rojo ?? null));
        $azul = $this->normalizarNombreCompetidor(($combate['b']['nombre'] ?? null) ?: ($resultado->competidor_azul ?? null));

        if ($ganador !== '' && $ganador === $rojo) {
            return 'a';
        }

        if ($ganador !== '' && $ganador === $azul) {
            return 'b';
        }

        if ((int) ($resultado->puntaje_rojo ?? 0) !== (int) ($resultado->puntaje_azul ?? 0)) {
            return (int) $resultado->puntaje_rojo > (int) $resultado->puntaje_azul ? 'a' : 'b';
        }

        return ($resultado->ganador_color ?? null) === 'azul' ? 'b' : 'a';
    }

    private function normalizarNombreCompetidor(?string $nombre): string
    {
        return trim(mb_strtolower(preg_replace('/\s+/', ' ', $nombre ?? '')));
    }

    private function prepararLlavesSorteos($sorteos): void
    {
        $sorteos->each(function ($sorteo) {
            $resultadosCompetencia = $this->resultadosCompetencia($sorteo);

            if ($resultadosCompetencia->isEmpty()) {
                $sorteo->llaves = $this->repararLlavesPendientes($sorteo);
            }

            $llaves = $this->llavesConPases($sorteo->llaves, $sorteo);
            $categoriaCompleta = $this->categoriaCompleta($llaves, $sorteo);

            $sorteo->setAttribute('llaves', $llaves);
            $sorteo->setAttribute('es_kata', $this->esSorteoKata($sorteo));
            $sorteo->setAttribute('resultados_competencia', $resultadosCompetencia);
            $sorteo->setAttribute('resultados_competencia_count', $resultadosCompetencia->count());
            $sorteo->setAttribute('categoria_completa', $categoriaCompleta);
            $sorteo->setAttribute('categoria_estado', $this->estadoCategoria($sorteo, $categoriaCompleta));
            $sorteo->setAttribute('podio_modal', $this->podioSorteo($llaves, $sorteo));
        });
    }

    private function repararLlavesPendientes(SorteoLlave $sorteo): array
    {
        $llaves = $sorteo->llaves ?? [];

        if (($llaves[0]['sistema'] ?? null) === 'round_robin' || ! isset($llaves[0]['combates'])) {
            return $llaves;
        }

        $torneo = Torneo::find($sorteo->torneo_id);

        if (! $torneo) {
            return $llaves;
        }

        $competidores = $this->competidoresCategoria($torneo, (int) $sorteo->categoria_id)->keyBy('id');
        $vistos = collect();

        foreach ($llaves[0]['combates'] as $matchIndex => $combate) {
            foreach (['a', 'b'] as $side) {
                $competidorId = $llaves[0]['combates'][$matchIndex][$side]['id'] ?? null;

                if (! $competidorId) {
                    continue;
                }

                $competidorId = (int) $competidorId;

                if ($vistos->contains($competidorId) || ! $competidores->has($competidorId)) {
                    $llaves[0]['combates'][$matchIndex][$side] = null;
                    continue;
                }

                $vistos->push($competidorId);
            }
        }

        $faltantes = $competidores->keys()
            ->map(fn ($id) => (int) $id)
            ->diff($vistos);

        foreach ($faltantes as $competidorFaltanteId) {
            foreach ($llaves[0]['combates'] as $matchIndex => $combate) {
                foreach (['a', 'b'] as $side) {
                    if (! empty($llaves[0]['combates'][$matchIndex][$side])) {
                        continue;
                    }

                    $llaves[0]['combates'][$matchIndex][$side] = $competidores->get($competidorFaltanteId);
                    continue 3;
                }
            }
        }

        foreach ($llaves[0]['combates'] as $matchIndex => $combate) {
            $tieneRojo = ! empty($llaves[0]['combates'][$matchIndex]['a']);
            $tieneAzul = ! empty($llaves[0]['combates'][$matchIndex]['b']);
            $llaves[0]['combates'][$matchIndex]['bye'] = $tieneRojo !== $tieneAzul;
        }

        if ($llaves !== ($sorteo->llaves ?? [])) {
            $sorteo->update(['llaves' => $llaves]);
        }

        return $llaves;
    }

    private function estadoCategoria(SorteoLlave $sorteo, bool $categoriaCompleta): string
    {
        if ($categoriaCompleta) {
            return 'realizada';
        }

        return $this->resultadosCompetencia($sorteo)->isNotEmpty() ? 'ejecucion' : 'pendiente';
    }

    private function categoriaCompleta(array $llaves, SorteoLlave $sorteo): bool
    {
        $resultadosPorIndice = $this->resultadosCompetencia($sorteo)->keyBy('indice_combate');

        if (! $this->esRoundRobin($llaves)) {
            $finalRound = count($llaves) - 1;
            $finalIndex = $this->indiceCombatePorPosicion($llaves, $finalRound, 0);

            return $finalIndex !== null && $resultadosPorIndice->has($finalIndex);
        }

        $indice = 0;
        $combatesNecesarios = 0;

        foreach ($llaves as $ronda) {
            foreach (($ronda['combates'] ?? []) as $combate) {
                $rojo = trim((string) ($combate['a']['nombre'] ?? ''));
                $azul = trim((string) ($combate['b']['nombre'] ?? ''));
                $byeReal = ($combate['bye'] ?? false) && (($rojo !== '') !== ($azul !== ''));
                $requiereResultado = ! $byeReal
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

        if ($this->esRoundRobin($llaves)) {
            return $this->podioRoundRobin($llaves, $sorteo, $podio);
        }

        $resultadosPorIndice = $this->resultadosCompetencia($sorteo)->keyBy('indice_combate');
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

    private function podioRoundRobin(array $llaves, SorteoLlave $sorteo, array $podio): array
    {
        $tabla = [];
        $resultadosPorIndice = $this->resultadosCompetencia($sorteo)->keyBy('indice_combate');
        $indice = 0;

        foreach ($llaves as $ronda) {
            foreach (($ronda['combates'] ?? []) as $combate) {
                $rojo = $this->nombreCompetidorPodio($combate['a'] ?? null);
                $azul = $this->nombreCompetidorPodio($combate['b'] ?? null);

                if ($rojo) {
                    $tabla[$rojo] ??= ['nombre' => $rojo, 'puntos' => 0, 'senshu' => 0, 'victorias' => 0];
                }

                if ($azul) {
                    $tabla[$azul] ??= ['nombre' => $azul, 'puntos' => 0, 'senshu' => 0, 'victorias' => 0];
                }

                $resultado = $resultadosPorIndice->get($indice);

                if ($resultado) {
                    $nombreRojo = $resultado->competidor_rojo ?: $rojo;
                    $nombreAzul = $resultado->competidor_azul ?: $azul;

                    if ($nombreRojo) {
                        $tabla[$nombreRojo] ??= ['nombre' => $nombreRojo, 'puntos' => 0, 'senshu' => 0, 'victorias' => 0];
                        $tabla[$nombreRojo]['puntos'] += (int) $resultado->puntaje_rojo;
                        $tabla[$nombreRojo]['senshu'] += ($resultado->senshu === 'rojo' || $resultado->senshu_rojo) ? 1 : 0;
                        $tabla[$nombreRojo]['victorias'] += $resultado->ganador_color === 'rojo' ? 1 : 0;
                    }

                    if ($nombreAzul) {
                        $tabla[$nombreAzul] ??= ['nombre' => $nombreAzul, 'puntos' => 0, 'senshu' => 0, 'victorias' => 0];
                        $tabla[$nombreAzul]['puntos'] += (int) $resultado->puntaje_azul;
                        $tabla[$nombreAzul]['senshu'] += ($resultado->senshu === 'azul' || $resultado->senshu_azul) ? 1 : 0;
                        $tabla[$nombreAzul]['victorias'] += $resultado->ganador_color === 'azul' ? 1 : 0;
                    }
                }

                $indice++;
            }
        }

        $ordenados = collect($tabla)
            ->sort(function ($a, $b) {
                return [$b['puntos'], $b['senshu'], $b['victorias'], $a['nombre']]
                    <=> [$a['puntos'], $a['senshu'], $a['victorias'], $b['nombre']];
            })
            ->values();

        $podio['oro'] = $ordenados[0]['nombre'] ?? '';
        $podio['plata'] = $ordenados[1]['nombre'] ?? '';
        $podio['bronce_1'] = $ordenados[2]['nombre'] ?? '';

        return $podio;
    }

    private function esRoundRobin(array $llaves): bool
    {
        return ($llaves[0]['sistema'] ?? null) === 'round_robin';
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

                if ($roundIndex > 0 && ! $this->combateTieneAsignacionManual($combate)) {
                    unset(
                        $llaves[$roundIndex]['combates'][$matchIndex]['a'],
                        $llaves[$roundIndex]['combates'][$matchIndex]['b']
                    );
                }
            }
        }

        return $llaves;
    }

    private function combateTieneAsignacionManual(array $combate): bool
    {
        foreach (['a', 'b'] as $side) {
            $nombre = $combate[$side]['nombre'] ?? '';

            if ($nombre !== '' && ! str_starts_with($nombre, 'Ganador')) {
                return true;
            }
        }

        return false;
    }

    private function puedePropagarGanadorA(?array $competidor): bool
    {
        $nombre = $competidor['nombre'] ?? '';

        return $nombre === '' || str_starts_with($nombre, 'Ganador');
    }

    private function propagarByes(array $llaves): array
    {
        foreach ($llaves as $roundIndex => $ronda) {
            if (! isset($llaves[$roundIndex + 1])) {
                continue;
            }

            foreach (($ronda['combates'] ?? []) as $matchIndex => $combate) {
                $tieneRojo = ! empty($combate['a']);
                $tieneAzul = ! empty($combate['b']);

                if (! (($combate['bye'] ?? false) && ($tieneRojo !== $tieneAzul))) {
                    continue;
                }

                $ganador = $combate['a'] ?? $combate['b'] ?? null;

                if (! $ganador) {
                    continue;
                }

                $nextMatch = (int) floor($matchIndex / 2);
                $nextSide = $matchIndex % 2 === 0 ? 'a' : 'b';

                if (isset($llaves[$roundIndex + 1]['combates'][$nextMatch])
                    && empty($llaves[$roundIndex + 1]['combates'][$nextMatch][$nextSide])) {
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
