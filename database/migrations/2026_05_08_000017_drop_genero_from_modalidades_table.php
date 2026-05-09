<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $modalidades = DB::table('modalidades')
            ->orderBy('id')
            ->get();
        $canonicalModalidades = [];
        $duplicateIds = [];

        foreach ($modalidades as $modalidad) {
            $key = $modalidad->torneo_id . '|' . mb_strtolower($modalidad->nombre);
            $canonicalModalidades[$key] ??= $modalidad->id;
            $canonicalId = $canonicalModalidades[$key];

            if ((int) $modalidad->id !== (int) $canonicalId) {
                DB::table('categorias')
                    ->where('modalidad_id', $modalidad->id)
                    ->update(['modalidad_id' => $canonicalId]);

                DB::table('inscripcion_competidor_modalidades')
                    ->where('modalidad_id', $modalidad->id)
                    ->update(['modalidad_id' => $canonicalId]);

                $duplicateIds[] = $modalidad->id;
            }
        }

        if ($duplicateIds !== []) {
            DB::table('modalidades')->whereIn('id', $duplicateIds)->delete();
        }

        foreach ([
            'modalidades_torneo_nombre_genero_unique',
            'modalidades_torneo_id_nombre_genero_unique',
            'modalidades_categoria_nombre_genero_unique',
        ] as $indexName) {
            try {
                Schema::table('modalidades', function (Blueprint $table) use ($indexName) {
                    $table->dropUnique($indexName);
                });
            } catch (Throwable $e) {
                //
            }
        }

        if (! Schema::hasColumn('modalidades', 'genero')) {
            try {
                Schema::table('modalidades', function (Blueprint $table) {
                    $table->unique(['torneo_id', 'nombre'], 'modalidades_torneo_nombre_unique');
                });
            } catch (Throwable $e) {
                //
            }

            return;
        }

        Schema::table('modalidades', function (Blueprint $table) {
            $table->dropColumn('genero');
        });

        try {
            Schema::table('modalidades', function (Blueprint $table) {
                $table->unique(['torneo_id', 'nombre'], 'modalidades_torneo_nombre_unique');
            });
        } catch (Throwable $e) {
            //
        }
    }

    public function down(): void
    {
        try {
            Schema::table('modalidades', function (Blueprint $table) {
                $table->dropUnique('modalidades_torneo_nombre_unique');
            });
        } catch (Throwable $e) {
            //
        }

        if (Schema::hasColumn('modalidades', 'genero')) {
            return;
        }

        Schema::table('modalidades', function (Blueprint $table) {
            $table->string('genero', 30)->nullable()->after('nombre');
        });

        try {
            Schema::table('modalidades', function (Blueprint $table) {
                $table->unique(['torneo_id', 'nombre', 'genero'], 'modalidades_torneo_nombre_genero_unique');
            });
        } catch (Throwable $e) {
            //
        }
    }
};
