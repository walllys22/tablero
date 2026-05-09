<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            Schema::table('categorias', function (Blueprint $table) {
                $table->dropUnique('categorias_modalidad_nombre_unique');
            });
        } catch (Throwable $e) {
            //
        }

        $categorias = DB::table('categorias')
            ->leftJoin('modalidades', 'modalidades.id', '=', 'categorias.modalidad_id')
            ->select('categorias.*', 'modalidades.nombre as modalidad_nombre')
            ->orderBy('categorias.id')
            ->get();

        foreach ($categorias as $categoria) {
            $parts = [];

            if ($categoria->edad_desde !== null && $categoria->edad_hasta !== null) {
                $parts[] = "{$categoria->edad_desde} a {$categoria->edad_hasta} años";
            } elseif ($categoria->edad_desde !== null) {
                $parts[] = "desde {$categoria->edad_desde} años";
            } elseif ($categoria->edad_hasta !== null) {
                $parts[] = "hasta {$categoria->edad_hasta} años";
            }

            if (! empty($categoria->genero)) {
                $parts[] = $categoria->genero;
            }

            $isKata = str_contains(mb_strtolower((string) $categoria->modalidad_nombre), 'kata');

            if (! $isKata && $categoria->peso_hasta !== null) {
                $pesoPhrase = null;

                if (preg_match('/\s+((menor|mayor)\s+o\s+igual\s+a\s+\d+([.,]\d+)?\s+kilos?)$/iu', $categoria->nombre, $matches)) {
                    $pesoPhrase = $matches[1];
                }

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

        $duplicates = DB::table('categorias')
            ->select('modalidad_id', 'nombre', DB::raw('MIN(id) as keep_id'))
            ->groupBy('modalidad_id', 'nombre')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            $duplicateIds = DB::table('categorias')
                ->where('modalidad_id', $duplicate->modalidad_id)
                ->where('nombre', $duplicate->nombre)
                ->where('id', '<>', $duplicate->keep_id)
                ->pluck('id');

            DB::table('inscripcion_competidor_modalidades')
                ->whereIn('categoria_id', $duplicateIds)
                ->update(['categoria_id' => $duplicate->keep_id]);

            DB::table('categorias')
                ->whereIn('id', $duplicateIds)
                ->delete();
        }

        Schema::table('categorias', function (Blueprint $table) {
            $table->unique(['modalidad_id', 'nombre'], 'categorias_modalidad_nombre_unique');
        });
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropUnique('categorias_modalidad_nombre_unique');
        });
    }
};
