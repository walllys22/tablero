<?php

namespace App\Support;

use App\Models\Torneo;

class DefaultTournamentCatalog
{
    public static function seedFor(Torneo $torneo): void
    {
        foreach (self::items() as $index => $item) {
            $modalidad = $torneo->modalidades()->firstOrCreate(
                ['nombre' => $item['modalidad']]
            );

            $categoria = $torneo->categorias()->firstOrCreate(
                [
                    'modalidad_id' => $modalidad->id,
                    'nombre' => self::categoryName($item),
                    'genero' => $item['genero'],
                    'edad_desde' => $item['edad_desde'] ?? null,
                    'edad_hasta' => $item['edad_hasta'] ?? null,
                    'peso_hasta' => $item['peso_hasta'] ?? null,
                ]
            );
        }
    }

    private static function categoryName(array $item): string
    {
        $parts = [];

        if (isset($item['edad_desde'], $item['edad_hasta'])) {
            $parts[] = $item['edad_desde'] . ' a ' . $item['edad_hasta'] . ' años';
        } elseif (isset($item['edad_desde'])) {
            $parts[] = 'desde ' . $item['edad_desde'] . ' años';
        } elseif (isset($item['edad_hasta'])) {
            $parts[] = 'hasta ' . $item['edad_hasta'] . ' años';
        }

        $parts[] = $item['genero'];

        if (isset($item['peso_hasta']) && ! str_contains(mb_strtolower($item['modalidad']), 'kata')) {
            preg_match('/\s+((menor|mayor)\s+o\s+igual\s+a\s+\d+([.,]\d+)?\s+kilos?)$/iu', $item['categoria'], $matches);
            $parts[] = $matches[1] ?? 'menor o igual a ' . $item['peso_hasta'] . ' kilos';
        }

        return trim(implode(' ', array_filter($parts)));
    }

    public static function items(): array
    {
        return [
            ['modalidad' => 'Kata Individual', 'categoria' => 'Infantil A', 'edad_desde' => 6, 'edad_hasta' => 7, 'genero' => 'Masculino'],
            ['modalidad' => 'Kata Individual', 'categoria' => 'Infantil A', 'edad_desde' => 6, 'edad_hasta' => 7, 'genero' => 'Femenino'],
            ['modalidad' => 'Kata Individual', 'categoria' => 'Infantil A', 'edad_desde' => 8, 'edad_hasta' => 9, 'genero' => 'Masculino'],
            ['modalidad' => 'Kata Individual', 'categoria' => 'Infantil A', 'edad_desde' => 8, 'edad_hasta' => 9, 'genero' => 'Femenino'],
            ['modalidad' => 'Kata Equipo', 'categoria' => 'Infantil', 'edad_hasta' => 11, 'genero' => 'Masculino'],
            ['modalidad' => 'Kata Equipo', 'categoria' => 'Infantil', 'edad_hasta' => 11, 'genero' => 'Femenino'],

            ['modalidad' => 'Kumite Individual', 'categoria' => '6 a 7 años menor o igual a 20 kilos', 'edad_desde' => 6, 'edad_hasta' => 7, 'peso_hasta' => 20, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '6 a 7 años menor o igual a 25 kilos', 'edad_desde' => 6, 'edad_hasta' => 7, 'peso_hasta' => 25, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '6 a 7 años menor o igual a 30 kilos', 'edad_desde' => 6, 'edad_hasta' => 7, 'peso_hasta' => 30, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '6 a 7 años mayor o igual a 30 kilos', 'edad_desde' => 6, 'edad_hasta' => 7, 'peso_hasta' => 30, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '8 a 9 años menor o igual a 25 kilos', 'edad_desde' => 8, 'edad_hasta' => 9, 'peso_hasta' => 25, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '8 a 9 años menor o igual a 30 kilos', 'edad_desde' => 8, 'edad_hasta' => 9, 'peso_hasta' => 30, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '8 a 9 años menor o igual a 35 kilos', 'edad_desde' => 8, 'edad_hasta' => 9, 'peso_hasta' => 35, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '8 a 9 años mayor o igual a 35 kilos', 'edad_desde' => 8, 'edad_hasta' => 9, 'peso_hasta' => 35, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '10 a 11 años menor o igual a 37 kilos', 'edad_desde' => 10, 'edad_hasta' => 11, 'peso_hasta' => 37, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '10 a 11 años menor o igual a 42 kilos', 'edad_desde' => 10, 'edad_hasta' => 11, 'peso_hasta' => 42, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '10 a 11 años menor o igual a 47 kilos', 'edad_desde' => 10, 'edad_hasta' => 11, 'peso_hasta' => 47, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '10 a 11 años mayor o igual a 47 kilos', 'edad_desde' => 10, 'edad_hasta' => 11, 'peso_hasta' => 47, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '12 a 13 años menor o igual a 42 kilos', 'edad_desde' => 12, 'edad_hasta' => 13, 'peso_hasta' => 42, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '12 a 13 años menor o igual a 47 kilos', 'edad_desde' => 12, 'edad_hasta' => 13, 'peso_hasta' => 47, 'genero' => 'Femenino'],

            ['modalidad' => 'Kumite Equipo', 'categoria' => '6 a 7 años menor o igual a 20 kilos', 'edad_desde' => 6, 'edad_hasta' => 7, 'peso_hasta' => 20, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '6 a 7 años menor o igual a 25 kilos', 'edad_desde' => 6, 'edad_hasta' => 7, 'peso_hasta' => 25, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '6 a 7 años menor o igual a 30 kilos', 'edad_desde' => 6, 'edad_hasta' => 7, 'peso_hasta' => 30, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '6 a 7 años mayor o igual a 30 kilos', 'edad_desde' => 6, 'edad_hasta' => 7, 'peso_hasta' => 30, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '8 a 9 años menor o igual a 25 kilos', 'edad_desde' => 8, 'edad_hasta' => 9, 'peso_hasta' => 25, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '8 a 9 años menor o igual a 30 kilos', 'edad_desde' => 8, 'edad_hasta' => 9, 'peso_hasta' => 30, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '8 a 9 años menor o igual a 35 kilos', 'edad_desde' => 8, 'edad_hasta' => 9, 'peso_hasta' => 35, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '8 a 9 años mayor o igual a 35 kilos', 'edad_desde' => 8, 'edad_hasta' => 9, 'peso_hasta' => 35, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '10 a 11 años menor o igual a 37 kilos', 'edad_desde' => 10, 'edad_hasta' => 11, 'peso_hasta' => 37, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '10 a 11 años menor o igual a 42 kilos', 'edad_desde' => 10, 'edad_hasta' => 11, 'peso_hasta' => 42, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '10 a 11 años menor o igual a 47 kilos', 'edad_desde' => 10, 'edad_hasta' => 11, 'peso_hasta' => 47, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '10 a 11 años mayor o igual a 47 kilos', 'edad_desde' => 10, 'edad_hasta' => 11, 'peso_hasta' => 47, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '12 a 13 años menor o igual a 42 kilos', 'edad_desde' => 12, 'edad_hasta' => 13, 'peso_hasta' => 42, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '12 a 13 años menor o igual a 47 kilos', 'edad_desde' => 12, 'edad_hasta' => 13, 'peso_hasta' => 47, 'genero' => 'Masculino'],
        ];
    }
}
