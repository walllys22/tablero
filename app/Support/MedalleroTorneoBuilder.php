<?php

namespace App\Support;

use App\Models\KumitePodio;
use App\Models\Organizacion;
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
}
