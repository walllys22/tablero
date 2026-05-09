<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('categorias')
            ->leftJoin('modalidades', 'modalidades.id', '=', 'categorias.modalidad_id')
            ->select('categorias.*', 'modalidades.nombre as modalidad_nombre')
            ->orderBy('categorias.id')
            ->chunk(100, function ($categorias) {
                foreach ($categorias as $categoria) {
                    $nombre = trim((string) $categoria->nombre);
                    $pesoPhrase = null;

                    if (preg_match('/\s+((menor|mayor)\s+o\s+igual\s+a\s+\d+([.,]\d+)?\s+kilos?)$/iu', $nombre, $matches)) {
                        $pesoPhrase = $matches[1];
                    }

                    $nombre = trim(preg_replace('/\s+(menor|mayor)\s+o\s+igual\s+a\s+\d+([.,]\d+)?\s+kilos?$/iu', '', $nombre));
                    $nombre = trim(preg_replace('/\s+(masculino|femenino|mixto)$/iu', '', $nombre));
                    $nombre = trim(preg_replace('/\s+(\d+\s+a\s+\d+\s+anos|desde\s+\d+\s+anos|hasta\s+\d+\s+anos)$/iu', '', $nombre));

                    $parts = [];

                    if ($nombre !== '') {
                        $parts[] = $nombre;
                    }

                    if ($categoria->edad_desde !== null && $categoria->edad_hasta !== null) {
                        $parts[] = "{$categoria->edad_desde} a {$categoria->edad_hasta} anos";
                    } elseif ($categoria->edad_desde !== null) {
                        $parts[] = "desde {$categoria->edad_desde} anos";
                    } elseif ($categoria->edad_hasta !== null) {
                        $parts[] = "hasta {$categoria->edad_hasta} anos";
                    }

                    if (! empty($categoria->genero)) {
                        $parts[] = $categoria->genero;
                    }

                    $isKata = str_contains(mb_strtolower((string) $categoria->modalidad_nombre), 'kata')
                        || str_contains(mb_strtolower($nombre), 'kata');

                    if (! $isKata && $categoria->peso_hasta !== null) {
                        $peso = rtrim(rtrim((string) $categoria->peso_hasta, '0'), '.');
                        $parts[] = $pesoPhrase ?: "menor o igual a {$peso} kilos";
                    }

                    if ($parts === []) {
                        continue;
                    }

                    DB::table('categorias')
                        ->where('id', $categoria->id)
                        ->update(['nombre' => trim(implode(' ', $parts))]);
                }
            });
    }

    public function down(): void
    {
        //
    }
};
