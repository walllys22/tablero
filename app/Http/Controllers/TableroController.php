<?php

namespace App\Http\Controllers;

use App\Models\KumiteCombateResultado;
use App\Models\KumitePodio;
use App\Models\SorteoLlave;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TableroController extends Controller
{
    public function kumite(Request $request)
    {
        $sorteo = $this->sorteoKumite($request);
        $llaves = $sorteo ? $this->llavesConResultados($sorteo) : [];
        $combatesKumite = [];
        $numeroLlave = 1;

        foreach ($llaves as $roundIndex => $ronda) {
            foreach (($ronda['combates'] ?? []) as $matchIndex => $combate) {
                $combatesKumite[] = [
                    'indice' => count($combatesKumite),
                    'round_index' => $roundIndex,
                    'match_index' => $matchIndex,
                    'ronda' => $ronda['nombre'] ?? 'Combate',
                    'numero_llave' => $numeroLlave++,
                    'rojo' => $combate['a']['nombre'] ?? '',
                    'azul' => $combate['b']['nombre'] ?? ($combate['bye'] ?? false ? 'BYE' : ''),
                    'bye' => (bool) ($combate['bye'] ?? false),
                    'realizado' => (bool) ($combate['realizado'] ?? false),
                    'ganador' => $combate['ganador']['nombre'] ?? null,
                ];
            }
        }

        $primerCombate = $combatesKumite[0] ?? [];

        $combateInicialKumite = [
            'sorteo_id' => $sorteo?->id,
            'siguiente_sorteo_id' => $this->siguienteSorteoKumite($sorteo)?->id,
            'modalidad' => $sorteo?->modalidad?->nombre ?? 'Kumite Individual',
            'categoria' => $sorteo?->categoria?->nombre ?? '',
            'rojo' => $primerCombate['rojo'] ?? '',
            'azul' => $primerCombate['azul'] ?? '',
        ];

        return view('kumite.tablero', compact('combateInicialKumite', 'combatesKumite'));
    }

    public function kata()
    {
        return view('kata.tablero');
    }

    public function podioKumite(Request $request)
    {
        $sorteo = $this->sorteoKumite($request);
        abort_unless($sorteo, 404);

        $llaves = $this->llavesConResultados($sorteo);
        $podio = $this->calcularPodio($llaves, $sorteo);
        $this->guardarPodioKumite($sorteo, $podio);
        $siguienteSorteo = $this->siguienteSorteoKumite($sorteo);

        return view('kumite.podio', compact('sorteo', 'podio', 'siguienteSorteo'));
    }

    public function guardarCombateKumite(Request $request)
    {
        $data = $request->validate([
            'sorteo_id' => ['required', 'exists:sorteo_llaves,id'],
            'numero_llave' => ['required', 'integer', 'min:1'],
            'indice_combate' => ['required', 'integer', 'min:0'],
            'competidor_rojo' => ['nullable', 'string', 'max:255'],
            'competidor_azul' => ['nullable', 'string', 'max:255'],
            'puntaje_rojo' => ['required', 'integer', 'min:0'],
            'puntaje_azul' => ['required', 'integer', 'min:0'],
            'faltas_rojo' => ['array'],
            'faltas_azul' => ['array'],
            'senshu' => ['nullable', 'in:rojo,azul'],
            'tecnicas_rojo' => ['array'],
            'tecnicas_azul' => ['array'],
            'ganador' => ['nullable', 'string', 'max:255'],
            'ganador_color' => ['nullable', 'in:rojo,azul'],
            'round_index' => ['required', 'integer', 'min:0'],
            'match_index' => ['required', 'integer', 'min:0'],
        ]);

        $sorteo = SorteoLlave::findOrFail($data['sorteo_id']);

        $resultado = KumiteCombateResultado::updateOrCreate(
            [
                'sorteo_llave_id' => $sorteo->id,
                'indice_combate' => $data['indice_combate'],
            ],
            [
                'numero_llave' => $data['numero_llave'],
                'competidor_rojo' => $data['competidor_rojo'] ?? null,
                'competidor_azul' => $data['competidor_azul'] ?? null,
                'puntaje_rojo' => $data['puntaje_rojo'],
                'puntaje_azul' => $data['puntaje_azul'],
                'faltas_rojo' => $data['faltas_rojo'] ?? [],
                'faltas_azul' => $data['faltas_azul'] ?? [],
                'senshu' => $data['senshu'] ?? null,
                'tecnicas_rojo' => $data['tecnicas_rojo'] ?? [],
                'tecnicas_azul' => $data['tecnicas_azul'] ?? [],
                'ganador' => $data['ganador'] ?? null,
                'ganador_color' => $data['ganador_color'] ?? null,
                'realizado_at' => Carbon::now(),
            ]
        );

        $this->marcarCombateRealizado($sorteo, $data);

        return response()->json([
            'success' => true,
            'resultado_id' => $resultado->id,
        ]);
    }

    private function sorteoKumite(Request $request): ?SorteoLlave
    {
        if ($request->filled('sorteo_id')) {
            return SorteoLlave::with(['modalidad', 'categoria', 'resultadosKumite'])
                ->whereHas('modalidad', function ($query) {
                    $query->where('nombre', 'like', '%Kumite%');
                })
                ->find($request->input('sorteo_id'));
        }

        $sorteos = SorteoLlave::with(['modalidad', 'categoria', 'resultadosKumite'])
            ->whereHas('modalidad', function ($query) {
                $query->where('nombre', 'like', '%Kumite%');
            })
            ->orderByRaw('COALESCE(area, 999)')
            ->orderByRaw('COALESCE(orden, 999999)')
            ->orderBy('id')
            ->get();

        return $sorteos->first(function ($sorteo) {
            return $this->tieneCombatesPendientes($sorteo);
        }) ?: SorteoLlave::with(['modalidad', 'categoria', 'resultadosKumite'])
            ->whereHas('modalidad', function ($query) {
                $query->where('nombre', 'like', '%Kumite%');
            })
            ->latest()
            ->first();
    }

    private function siguienteSorteoKumite(?SorteoLlave $actual): ?SorteoLlave
    {
        if (! $actual) {
            return null;
        }

        $query = SorteoLlave::with(['modalidad', 'categoria', 'resultadosKumite'])
            ->where('torneo_id', $actual->torneo_id)
            ->where('id', '!=', $actual->id)
            ->whereHas('modalidad', function ($query) {
                $query->where('nombre', 'like', '%Kumite%');
            })
            ->orderByRaw('CASE WHEN COALESCE(orden, 999999) > COALESCE(?, 999999) THEN 0 ELSE 1 END', [$actual->orden])
            ->orderByRaw('COALESCE(orden, 999999)')
            ->orderBy('id');

        if ($actual->area) {
            $query->where('area', $actual->area);
        }

        return $query->get()->first(function ($sorteo) {
            return $this->tieneCombatesPendientes($sorteo);
        });
    }

    private function tieneCombatesPendientes(SorteoLlave $sorteo): bool
    {
        foreach ($this->llavesConResultados($sorteo) as $ronda) {
            foreach (($ronda['combates'] ?? []) as $combate) {
                $rojo = trim((string) ($combate['a']['nombre'] ?? ''));
                $azul = trim((string) ($combate['b']['nombre'] ?? ''));

                if (($combate['realizado'] ?? false) || ($combate['bye'] ?? false)) {
                    continue;
                }

                if ($rojo !== '' && $azul !== '' && strtoupper($rojo) !== 'BYE' && strtoupper($azul) !== 'BYE') {
                    return true;
                }
            }
        }

        return false;
    }

    private function llavesConResultados(SorteoLlave $sorteo): array
    {
        $llaves = $this->propagarByes($sorteo->llaves);

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

            $llaves[$roundIndex]['combates'][$matchIndex]['realizado'] = true;
            $llaves[$roundIndex]['combates'][$matchIndex]['ganador'] = [
                'nombre' => $resultado->ganador,
                'color' => $resultado->ganador_color,
                'fecha' => optional($resultado->realizado_at)->toDateTimeString(),
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

    private function marcarCombateRealizado(SorteoLlave $sorteo, array $data): void
    {
        $llaves = $sorteo->llaves;
        $roundIndex = (int) $data['round_index'];
        $matchIndex = (int) $data['match_index'];

        if (! isset($llaves[$roundIndex]['combates'][$matchIndex])) {
            return;
        }

        $combate = $llaves[$roundIndex]['combates'][$matchIndex];
        $ganadorSide = ($data['ganador_color'] ?? null) === 'rojo' ? 'a' : 'b';
        $ganadorCompetidor = $combate[$ganadorSide] ?? [
            'nombre' => $data['ganador'] ?? null,
            'organizacion' => '',
            'organizacion_id' => null,
        ];

        $llaves[$roundIndex]['combates'][$matchIndex]['realizado'] = true;
        $llaves[$roundIndex]['combates'][$matchIndex]['ganador'] = [
            'nombre' => $data['ganador'] ?? null,
            'color' => $data['ganador_color'] ?? null,
            'fecha' => Carbon::now()->toDateTimeString(),
        ];

        $nextRound = $roundIndex + 1;
        $nextMatch = (int) floor($matchIndex / 2);
        $nextSide = $matchIndex % 2 === 0 ? 'a' : 'b';

        if (isset($llaves[$nextRound]['combates'][$nextMatch])) {
            $llaves[$nextRound]['combates'][$nextMatch][$nextSide] = $ganadorCompetidor;
        }

        $sorteo->update(['llaves' => $llaves]);
    }

    private function calcularPodio(array $llaves, SorteoLlave $sorteo): array
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

    private function guardarPodioKumite(SorteoLlave $sorteo, array $podio): void
    {
        KumitePodio::updateOrCreate(
            ['sorteo_llave_id' => $sorteo->id],
            [
                'oro' => $podio['oro'] ?: null,
                'plata' => $podio['plata'] ?: null,
                'bronce_1' => $podio['bronce_1'] ?: null,
                'bronce_2' => $podio['bronce_2'] ?: null,
                'generado_at' => Carbon::now(),
            ]
        );
    }

    private function nombreCompetidorPodio(?array $competidor): string
    {
        $nombre = $competidor['nombre'] ?? '';

        return in_array($nombre, ['', 'BYE', 'Competidor'], true) || str_starts_with($nombre, 'Ganador')
            ? ''
            : $nombre;
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
}
