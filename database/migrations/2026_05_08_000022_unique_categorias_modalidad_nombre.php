<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

            if ($duplicateIds->isEmpty()) {
                continue;
            }

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
