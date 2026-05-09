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
                    if (empty($categoria->genero)) {
                        continue;
                    }

                    $nombre = trim((string) $categoria->nombre);
                    $pesoPhrase = '';

                    if (preg_match('/\s+((menor|mayor)\s+o\s+igual\s+a\s+\d+([.,]\d+)?\s+kilos?)$/iu', $nombre, $matches)) {
                        $pesoPhrase = $matches[1];
                        $nombre = trim(preg_replace('/\s+(menor|mayor)\s+o\s+igual\s+a\s+\d+([.,]\d+)?\s+kilos?$/iu', '', $nombre));
                    } elseif ($categoria->peso_hasta !== null && ! str_contains(mb_strtolower((string) $categoria->modalidad_nombre), 'kata')) {
                        $peso = rtrim(rtrim((string) $categoria->peso_hasta, '0'), '.');
                        $pesoPhrase = "menor o igual a {$peso} kilos";
                    }

                    $nombre = trim(preg_replace('/\s+(masculino|femenino|mixto)$/iu', '', $nombre));
                    $parts = [$nombre, $categoria->genero];

                    if ($pesoPhrase !== '' && ! str_contains(mb_strtolower($nombre), 'kata')) {
                        $parts[] = $pesoPhrase;
                    }

                    DB::table('categorias')
                        ->where('id', $categoria->id)
                        ->update(['nombre' => trim(implode(' ', array_filter($parts)))]);
                }
            });
    }

    public function down(): void
    {
        DB::table('categorias')
            ->whereNotNull('genero')
            ->orderBy('id')
            ->chunk(100, function ($categorias) {
                foreach ($categorias as $categoria) {
                    $nombre = trim(preg_replace('/\s+(masculino|femenino|mixto)(\s+(menor|mayor)\s+o\s+igual\s+a\s+\d+([.,]\d+)?\s+kilos?)?$/iu', '$2', $categoria->nombre));

                    DB::table('categorias')
                        ->where('id', $categoria->id)
                        ->update(['nombre' => $nombre]);
                }
            });
    }
};
