<?php

namespace App\Support;

use App\Models\Torneo;

class DefaultTournamentCatalog
{
    public static function seedFor(Torneo $torneo): void
    {
        foreach (self::items() as $index => $item) {
            $modalidad = $torneo->modalidades()->firstOrCreate([
                'nombre' => $item['modalidad'],
                'genero' => $item['genero'],
            ]);

            $categoria = $torneo->categorias()->firstOrCreate(
                [
                    'modalidad_id' => $modalidad->id,
                    'nombre' => $item['categoria'],
                    'genero' => $item['genero'],
                    'edad_desde' => $item['edad_desde'] ?? null,
                    'edad_hasta' => $item['edad_hasta'] ?? null,
                    'peso_desde' => $item['peso_desde'] ?? null,
                    'peso_hasta' => $item['peso_hasta'] ?? null,
                    'grado' => $item['grado'] ?? null,
                ],
                ['orden' => $index + 1]
            );
        }
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

            ['modalidad' => 'Kumite Individual', 'categoria' => '6 a 7 anos', 'edad_desde' => 6, 'edad_hasta' => 7, 'peso_hasta' => 20, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '6 a 7 anos', 'edad_desde' => 6, 'edad_hasta' => 7, 'peso_hasta' => 25, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '6 a 7 anos', 'edad_desde' => 6, 'edad_hasta' => 7, 'peso_hasta' => 30, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '6 a 7 anos', 'edad_desde' => 6, 'edad_hasta' => 7, 'peso_desde' => 30, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '8 a 9 anos', 'edad_desde' => 8, 'edad_hasta' => 9, 'peso_hasta' => 25, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '8 a 9 anos', 'edad_desde' => 8, 'edad_hasta' => 9, 'peso_hasta' => 30, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '8 a 9 anos', 'edad_desde' => 8, 'edad_hasta' => 9, 'peso_hasta' => 35, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '8 a 9 anos', 'edad_desde' => 8, 'edad_hasta' => 9, 'peso_desde' => 35, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '10 a 11 anos', 'edad_desde' => 10, 'edad_hasta' => 11, 'peso_hasta' => 37, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '10 a 11 anos', 'edad_desde' => 10, 'edad_hasta' => 11, 'peso_hasta' => 42, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '10 a 11 anos', 'edad_desde' => 10, 'edad_hasta' => 11, 'peso_hasta' => 47, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '10 a 11 anos', 'edad_desde' => 10, 'edad_hasta' => 11, 'peso_desde' => 47, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '12 a 13 anos', 'edad_desde' => 12, 'edad_hasta' => 13, 'peso_hasta' => 42, 'genero' => 'Femenino'],
            ['modalidad' => 'Kumite Individual', 'categoria' => '12 a 13 anos', 'edad_desde' => 12, 'edad_hasta' => 13, 'peso_hasta' => 47, 'genero' => 'Femenino'],

            ['modalidad' => 'Kumite Equipo', 'categoria' => '6 a 7 anos', 'edad_desde' => 6, 'edad_hasta' => 7, 'peso_hasta' => 20, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '6 a 7 anos', 'edad_desde' => 6, 'edad_hasta' => 7, 'peso_hasta' => 25, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '6 a 7 anos', 'edad_desde' => 6, 'edad_hasta' => 7, 'peso_hasta' => 30, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '6 a 7 anos', 'edad_desde' => 6, 'edad_hasta' => 7, 'peso_desde' => 30, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '8 a 9 anos', 'edad_desde' => 8, 'edad_hasta' => 9, 'peso_hasta' => 25, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '8 a 9 anos', 'edad_desde' => 8, 'edad_hasta' => 9, 'peso_hasta' => 30, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '8 a 9 anos', 'edad_desde' => 8, 'edad_hasta' => 9, 'peso_hasta' => 35, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '8 a 9 anos', 'edad_desde' => 8, 'edad_hasta' => 9, 'peso_desde' => 35, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '10 a 11 anos', 'edad_desde' => 10, 'edad_hasta' => 11, 'peso_hasta' => 37, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '10 a 11 anos', 'edad_desde' => 10, 'edad_hasta' => 11, 'peso_hasta' => 42, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '10 a 11 anos', 'edad_desde' => 10, 'edad_hasta' => 11, 'peso_hasta' => 47, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '10 a 11 anos', 'edad_desde' => 10, 'edad_hasta' => 11, 'peso_desde' => 47, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '12 a 13 anos', 'edad_desde' => 12, 'edad_hasta' => 13, 'peso_hasta' => 42, 'genero' => 'Masculino'],
            ['modalidad' => 'Kumite Equipo', 'categoria' => '12 a 13 anos', 'edad_desde' => 12, 'edad_hasta' => 13, 'peso_hasta' => 47, 'genero' => 'Masculino'],
        ];
    }
}
