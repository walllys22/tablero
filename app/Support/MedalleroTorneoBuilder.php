<?php

namespace App\Support;

use App\Models\KumitePodio;
use App\Models\Organizacion;
use App\Models\SorteoLlave;
use App\Models\Torneo;

class MedalleroTorneoBuilder
{
    public static function build(?Torneo $torneo): array
    {
        if (! $torneo) {
            return [
                'medalleroGeneral' => collect(),
                'medallero' => collect(),
                'medalleroKata' => collect(),
            ];
        }

        $organizaciones = Organizacion::with('estilo')
            ->whereHas('inscripciones', function ($query) use ($torneo) {
                $query->where('torneo_id', $torneo->id);
            })
            ->orderBy('nombre')
            ->get();

        $crearMedallero = function () use ($organizaciones) {
            return $organizaciones->mapWithKeys(function ($organizacion) {
                return [
                    $organizacion->id => [
                        'organizacion' => $organizacion,
                        'oro' => 0,
                        'plata' => 0,
                        'bronce' => 0,
                        'total' => 0,
                        'podios' => collect(),
                    ],
                ];
            });
        };

        $medalleroGeneral = $crearMedallero();
        $medallero = $crearMedallero();
        $medalleroKata = $crearMedallero();

        KumitePodio::with(['sorteoLlave.categoria', 'sorteoLlave.modalidad'])
            ->whereHas('sorteoLlave', function ($query) use ($torneo) {
                $query->where('torneo_id', $torneo->id);
            })
            ->get()
            ->each(function ($podio) use (&$medalleroGeneral, &$medallero, &$medalleroKata) {
                $sorteo = $podio->sorteoLlave;
                $categoria = $sorteo?->categoria;
                $modalidad = $sorteo?->modalidad;
                $categoriaNombre = 'Sin categoria';

                if ($categoria) {
                    $categoriaNombre = trim(CategoriaNameFormatter::format($categoria, $modalidad?->nombre));
                    $categoriaNombre = $categoriaNombre !== '' ? $categoriaNombre : trim((string) $categoria->nombre);
                }

                $modalidadNombre = $modalidad?->nombre ?? 'Kumite';
                $organizacionesPorCompetidor = collect($sorteo?->llaves ?? [])
                    ->flatMap(fn ($ronda) => $ronda['combates'] ?? [])
                    ->flatMap(fn ($combate) => collect([$combate['a'] ?? null, $combate['b'] ?? null]))
                    ->filter(fn ($competidor) => is_array($competidor) && ! empty($competidor['nombre']) && ! empty($competidor['organizacion_id']))
                    ->mapWithKeys(fn ($competidor) => [
                        mb_strtolower(trim($competidor['nombre'])) => (int) $competidor['organizacion_id'],
                    ]);
                $esKata = str_contains(mb_strtolower($modalidadNombre), 'kata');

                foreach (['oro' => 'oro', 'plata' => 'plata', 'bronce_1' => 'bronce', 'bronce_2' => 'bronce'] as $campo => $medalla) {
                    $nombre = trim((string) $podio->{$campo});

                    if ($nombre === '') {
                        continue;
                    }

                    $organizacionId = $organizacionesPorCompetidor->get(mb_strtolower($nombre));

                    if (! $organizacionId || ! $medalleroGeneral->has($organizacionId)) {
                        continue;
                    }

                    $detallePodio = [
                        'categoria' => $categoriaNombre,
                        'modalidad' => $modalidadNombre,
                        'medalla' => $medalla,
                        'competidor' => $nombre,
                    ];

                    self::sumarMedalla($medalleroGeneral, $organizacionId, $medalla, $detallePodio);

                    if ($esKata) {
                        self::sumarMedalla($medalleroKata, $organizacionId, $medalla, $detallePodio);
                    } else {
                        self::sumarMedalla($medallero, $organizacionId, $medalla, $detallePodio);
                    }
                }
            });

        SorteoLlave::with(['categoria', 'modalidad', 'resultadosKata'])
            ->where('torneo_id', $torneo->id)
            ->whereHas('modalidad', fn ($query) => $query->where('nombre', 'like', '%Kata%'))
            ->get()
            ->each(function ($sorteo) use (&$medalleroGeneral, &$medalleroKata) {
                $llaves = self::llavesConResultadosKata($sorteo);

                if (! self::categoriaCompleta($llaves)) {
                    return;
                }

                $podio = self::podioLlaves($llaves);
                $categoria = $sorteo->categoria;
                $modalidad = $sorteo->modalidad;
                $modalidadNombre = $modalidad?->nombre ?? 'Kata';
                $categoriaNombre = 'Sin categoria';

                if ($categoria) {
                    $categoriaNombre = trim(CategoriaNameFormatter::format($categoria, $modalidadNombre));
                    $categoriaNombre = $categoriaNombre !== '' ? $categoriaNombre : trim((string) $categoria->nombre);
                }

                $organizacionesPorCompetidor = self::organizacionesPorCompetidor($llaves);

                foreach (['oro' => 'oro', 'plata' => 'plata', 'bronce_1' => 'bronce', 'bronce_2' => 'bronce'] as $campo => $medalla) {
                    $nombre = trim((string) ($podio[$campo] ?? ''));

                    if ($nombre === '') {
                        continue;
                    }

                    $organizacionId = $organizacionesPorCompetidor->get(mb_strtolower($nombre));

                    if (! $organizacionId || ! $medalleroGeneral->has($organizacionId)) {
                        continue;
                    }

                    $detallePodio = [
                        'categoria' => $categoriaNombre,
                        'modalidad' => $modalidadNombre,
                        'medalla' => $medalla,
                        'competidor' => $nombre,
                    ];

                    self::sumarMedalla($medalleroGeneral, $organizacionId, $medalla, $detallePodio);
                    self::sumarMedalla($medalleroKata, $organizacionId, $medalla, $detallePodio);
                }
            });

        return [
            'medalleroGeneral' => self::ordenar($medalleroGeneral),
            'medallero' => self::ordenar($medallero),
            'medalleroKata' => self::ordenar($medalleroKata),
        ];
    }

    private static function sumarMedalla($medallero, int $organizacionId, string $medalla, array $detallePodio): void
    {
        $fila = $medallero->get($organizacionId);
        $fila[$medalla]++;
        $fila['total']++;
        $fila['podios']->push($detallePodio);
        $medallero->put($organizacionId, $fila);
    }

    private static function ordenar($medallero)
    {
        $tieneMedallas = $medallero->contains(fn ($fila) => $fila['total'] > 0);

        return $tieneMedallas
            ? $medallero->sort(function ($a, $b) {
                return [$b['oro'], $b['plata'], $b['bronce'], $a['organizacion']->nombre]
                    <=> [$a['oro'], $a['plata'], $a['bronce'], $b['organizacion']->nombre];
            })->values()
            : $medallero->sortBy(fn ($fila) => $fila['organizacion']->nombre)->values();
    }

    private static function llavesConResultadosKata(SorteoLlave $sorteo): array
    {
        $llaves = self::propagarByes(self::reiniciarLlavesBase($sorteo->llaves ?? []));

        foreach ($sorteo->resultadosKata->sortBy('indice_combate') as $resultado) {
            $position = self::posicionCombatePorIndice($llaves, (int) $resultado->indice_combate);

            if (! $position) {
                continue;
            }

            [$roundIndex, $matchIndex] = $position;
            $combate = $llaves[$roundIndex]['combates'][$matchIndex];
            $ganadorSide = self::ladoGanadorKata($resultado, $combate);
            $ganadorCompetidor = $combate[$ganadorSide] ?? [
                'nombre' => $resultado->ganador,
                'organizacion' => '',
                'organizacion_id' => null,
            ];

            $llaves[$roundIndex]['combates'][$matchIndex]['realizado'] = true;
            $llaves[$roundIndex]['combates'][$matchIndex]['ganador'] = [
                'nombre' => $resultado->ganador,
                'color' => $resultado->ganador_color,
            ];

            $nextRound = $roundIndex + 1;
            $nextMatch = (int) floor($matchIndex / 2);
            $nextSide = $matchIndex % 2 === 0 ? 'a' : 'b';

            if (isset($llaves[$nextRound]['combates'][$nextMatch])
                && self::puedePropagarGanadorA($llaves[$nextRound]['combates'][$nextMatch][$nextSide] ?? null)) {
                $llaves[$nextRound]['combates'][$nextMatch][$nextSide] = $ganadorCompetidor;
            }

            $llaves = self::propagarByes($llaves);
        }

        return $llaves;
    }

    private static function ladoGanadorKata($resultado, array $combate): string
    {
        $ganador = self::normalizarNombreCompetidor($resultado->ganador ?? null);
        $rojo = self::normalizarNombreCompetidor(($combate['a']['nombre'] ?? null) ?: ($resultado->competidor_rojo ?? null));
        $azul = self::normalizarNombreCompetidor(($combate['b']['nombre'] ?? null) ?: ($resultado->competidor_azul ?? null));

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

    private static function normalizarNombreCompetidor(?string $nombre): string
    {
        return trim(mb_strtolower(preg_replace('/\s+/', ' ', $nombre ?? '')));
    }

    private static function reiniciarLlavesBase(array $llaves): array
    {
        foreach ($llaves as $roundIndex => $ronda) {
            foreach (($ronda['combates'] ?? []) as $matchIndex => $combate) {
                unset(
                    $llaves[$roundIndex]['combates'][$matchIndex]['realizado'],
                    $llaves[$roundIndex]['combates'][$matchIndex]['ganador']
                );

                if ($roundIndex > 0 && ! self::combateTieneAsignacionManual($combate)) {
                    unset(
                        $llaves[$roundIndex]['combates'][$matchIndex]['a'],
                        $llaves[$roundIndex]['combates'][$matchIndex]['b']
                    );
                }
            }
        }

        return $llaves;
    }

    private static function combateTieneAsignacionManual(array $combate): bool
    {
        foreach (['a', 'b'] as $side) {
            $nombre = $combate[$side]['nombre'] ?? '';

            if ($nombre !== '' && ! str_starts_with($nombre, 'Ganador')) {
                return true;
            }
        }

        return false;
    }

    private static function puedePropagarGanadorA(?array $competidor): bool
    {
        $nombre = $competidor['nombre'] ?? '';

        return $nombre === '' || str_starts_with($nombre, 'Ganador');
    }

    private static function propagarByes(array $llaves): array
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

    private static function posicionCombatePorIndice(array $llaves, int $indice): ?array
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

    private static function categoriaCompleta(array $llaves): bool
    {
        if (($llaves[0]['sistema'] ?? null) !== 'round_robin') {
            $finalRound = count($llaves) - 1;
            $finalIndex = self::posicionCombatePorIndice($llaves, $finalRound, 0);
            $final = $llaves[$finalRound]['combates'][0] ?? null;

            return $finalIndex !== null && ! empty($final['realizado']);
        }

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

                if (! $requiereResultado) {
                    continue;
                }

                $combatesNecesarios++;

                if (empty($combate['realizado'])) {
                    return false;
                }
            }
        }

        return $combatesNecesarios > 0;
    }

    private static function podioLlaves(array $llaves): array
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

        $finalRound = count($llaves) - 1;
        $final = $llaves[$finalRound]['combates'][0] ?? null;

        if ($final && ! empty($final['ganador']['nombre'])) {
            $ganador = $final['ganador']['nombre'];
            $rojo = self::nombreCompetidorPodio($final['a'] ?? null);
            $azul = self::nombreCompetidorPodio($final['b'] ?? null);

            $podio['oro'] = $ganador;
            $podio['plata'] = $ganador === $rojo ? $azul : $rojo;
        }

        if (count($llaves) < 2) {
            return $podio;
        }

        $semifinalRound = count($llaves) - 2;
        $bronces = [];

        foreach (($llaves[$semifinalRound]['combates'] ?? []) as $combate) {
            if (empty($combate['ganador']['nombre'])) {
                continue;
            }

            $ganador = $combate['ganador']['nombre'];
            $rojo = self::nombreCompetidorPodio($combate['a'] ?? null);
            $azul = self::nombreCompetidorPodio($combate['b'] ?? null);
            $perdedor = $ganador === $rojo ? $azul : $rojo;

            if ($perdedor) {
                $bronces[] = $perdedor;
            }
        }

        $podio['bronce_1'] = $bronces[0] ?? '';
        $podio['bronce_2'] = $bronces[1] ?? '';

        return $podio;
    }

    private static function organizacionesPorCompetidor(array $llaves)
    {
        return collect($llaves)
            ->flatMap(fn ($ronda) => $ronda['combates'] ?? [])
            ->flatMap(fn ($combate) => collect([$combate['a'] ?? null, $combate['b'] ?? null]))
            ->filter(fn ($competidor) => is_array($competidor) && ! empty($competidor['nombre']) && ! empty($competidor['organizacion_id']))
            ->mapWithKeys(fn ($competidor) => [
                mb_strtolower(trim($competidor['nombre'])) => (int) $competidor['organizacion_id'],
            ]);
    }

    private static function nombreCompetidorPodio(?array $competidor): string
    {
        $nombre = $competidor['nombre'] ?? '';

        return in_array($nombre, ['', 'BYE', 'Competidor', 'Pendiente'], true) || str_starts_with($nombre, 'Ganador')
            ? ''
            : $nombre;
    }
}
