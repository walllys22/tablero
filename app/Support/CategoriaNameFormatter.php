<?php

namespace App\Support;

use App\Models\Categoria;

class CategoriaNameFormatter
{
    public static function format(Categoria $categoria, ?string $modalidadNombre = null): string
    {
        $isKata = str_contains(mb_strtolower((string) $modalidadNombre), 'kata');

        if (! $categoria->edad_desde && ! $categoria->edad_hasta && ! $categoria->genero && ($categoria->peso_hasta === null || $isKata)) {
            return self::cleanName((string) $categoria->nombre);
        }

        $parts = [];

        if ($categoria->edad_desde && $categoria->edad_hasta) {
            $parts[] = "{$categoria->edad_desde} a {$categoria->edad_hasta} años";
        } elseif ($categoria->edad_desde) {
            $parts[] = "desde {$categoria->edad_desde} años";
        } elseif ($categoria->edad_hasta) {
            $parts[] = "hasta {$categoria->edad_hasta} años";
        }

        if ($categoria->genero) {
            $parts[] = $categoria->genero;
        }

        if (! $isKata && $categoria->peso_hasta !== null) {
            $operator = self::isPesoMinimo((string) $categoria->nombre) ? '≥' : '≤';
            $parts[] = "{$operator} a " . self::formatPeso((float) $categoria->peso_hasta);
        }

        return trim(implode(' ', $parts)) ?: self::cleanName((string) $categoria->nombre);
    }

    private static function cleanName(string $name): string
    {
        return str_replace(
            ["a\xC3\x83\xC6\x92\xC3\x82\xC2\xB1os", "a\xC3\x83\xC2\xB1os", 'anos', 'menor o igual', 'mayor o igual'],
            ['años', 'años', 'años', '≤', '≥'],
            $name
        );
    }

    private static function isPesoMinimo(string $name): bool
    {
        $name = mb_strtolower($name);

        return str_contains($name, 'mayor o igual') || str_contains($name, '≥');
    }

    private static function formatPeso(float $peso): string
    {
        $kilos = (int) floor($peso);
        $gramos = (int) round(($peso - $kilos) * 1000);

        if ($gramos === 1000) {
            $kilos++;
            $gramos = 0;
        }

        if ($gramos === 0) {
            return "{$kilos} Kilos";
        }

        return "{$kilos} Kilos y {$gramos} gramos";
    }
}
