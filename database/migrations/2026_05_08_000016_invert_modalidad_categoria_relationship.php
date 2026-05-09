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
            Schema::table('inscripcion_competidor_modalidades', function (Blueprint $table) {
                $table->index('modalidad_id', 'icm_modalidad_id_index');
            });
        } catch (Throwable $e) {
            //
        }

        try {
            Schema::table('inscripcion_competidor_modalidades', function (Blueprint $table) {
                $table->dropUnique('icm_competidor_modalidad_unique');
            });
        } catch (Throwable $e) {
            //
        }

        if (! Schema::hasColumn('inscripcion_competidor_modalidades', 'categoria_id')) {
            Schema::table('inscripcion_competidor_modalidades', function (Blueprint $table) {
                $table->unsignedBigInteger('categoria_id')->nullable()->after('modalidad_id');
            });
        }

        try {
            Schema::table('inscripcion_competidor_modalidades', function (Blueprint $table) {
                $table->index('categoria_id', 'icm_categoria_id_index');
            });
        } catch (Throwable $e) {
            //
        }

        if (! Schema::hasColumn('categorias', 'modalidad_id')) {
            Schema::table('categorias', function (Blueprint $table) {
                $table->unsignedBigInteger('modalidad_id')->nullable()->after('torneo_id');
                $table->index('modalidad_id', 'categorias_modalidad_id_index');
            });
        }

        $canonicalModalidades = [];
        $categoriaIdsByModalidad = [];
        $modalidades = DB::table('modalidades')
            ->orderBy('id')
            ->get();

        foreach ($modalidades as $modalidad) {
            $key = $modalidad->torneo_id . '|' . mb_strtolower($modalidad->nombre);
            $canonicalModalidades[$key] ??= $modalidad->id;
            $canonicalId = $canonicalModalidades[$key];

            if (! empty($modalidad->categoria_id)) {
                $categoria = DB::table('categorias')->where('id', $modalidad->categoria_id)->first();

                if ($categoria) {
                    if (empty($categoria->modalidad_id) || (int) $categoria->modalidad_id === (int) $canonicalId) {
                        DB::table('categorias')
                            ->where('id', $categoria->id)
                            ->update(['modalidad_id' => $canonicalId]);
                        $categoriaIdsByModalidad[$modalidad->id] = $categoria->id;
                    } else {
                        $newCategoria = (array) $categoria;
                        unset($newCategoria['id']);
                        $newCategoria['modalidad_id'] = $canonicalId;
                        $newCategoria['created_at'] = now();
                        $newCategoria['updated_at'] = now();

                        $categoriaIdsByModalidad[$modalidad->id] = DB::table('categorias')->insertGetId($newCategoria);
                    }
                }
            }

            $categoriaId = $categoriaIdsByModalidad[$modalidad->id] ?? $modalidad->categoria_id;

            if ((int) $modalidad->id !== (int) $canonicalId) {
                DB::table('inscripcion_competidor_modalidades')
                    ->where('modalidad_id', $modalidad->id)
                    ->update([
                        'modalidad_id' => $canonicalId,
                        'categoria_id' => $categoriaId,
                    ]);
            } else {
                DB::table('inscripcion_competidor_modalidades')
                    ->where('modalidad_id', $modalidad->id)
                    ->whereNull('categoria_id')
                    ->update(['categoria_id' => $categoriaId]);
            }
        }

        $duplicateIds = [];
        $seen = [];

        foreach ($modalidades as $modalidad) {
            $key = $modalidad->torneo_id . '|' . mb_strtolower($modalidad->nombre);

            if (isset($seen[$key])) {
                $duplicateIds[] = $modalidad->id;
            } else {
                $seen[$key] = true;
            }
        }

        if ($duplicateIds !== []) {
            DB::table('modalidades')->whereIn('id', $duplicateIds)->delete();
        }

        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('modalidades', function (Blueprint $table) {
            $table->dropUnique('modalidades_categoria_nombre_genero_unique');
            $table->dropForeign('modalidades_categoria_fk');
            $table->dropColumn('categoria_id');
            $table->unique(['torneo_id', 'nombre'], 'modalidades_torneo_nombre_unique');
        });

        Schema::table('categorias', function (Blueprint $table) {
            $table->foreign('modalidad_id', 'categorias_modalidad_fk')
                ->references('id')
                ->on('modalidades')
                ->cascadeOnDelete();
        });

        Schema::table('inscripcion_competidor_modalidades', function (Blueprint $table) {
            $table->foreign('categoria_id', 'icm_categoria_fk')
                ->references('id')
                ->on('categorias')
                ->cascadeOnDelete();
            $table->unique(['inscripcion_competidor_id', 'modalidad_id', 'categoria_id'], 'icm_competidor_modalidad_categoria_unique');
        });
    }

    public function down(): void
    {
        Schema::table('inscripcion_competidor_modalidades', function (Blueprint $table) {
            $table->dropUnique('icm_competidor_modalidad_categoria_unique');
            $table->dropForeign('icm_categoria_fk');
            $table->dropIndex('icm_categoria_id_index');
        });

        Schema::table('categorias', function (Blueprint $table) {
            $table->dropForeign('categorias_modalidad_fk');
            $table->dropIndex('categorias_modalidad_id_index');
        });

        Schema::table('modalidades', function (Blueprint $table) {
            $table->dropUnique('modalidades_torneo_nombre_unique');
            $table->foreignId('categoria_id')
                ->nullable()
                ->after('torneo_id')
                ->constrained('categorias', 'id', 'modalidades_categoria_fk')
                ->nullOnDelete();
            $table->unique(['torneo_id', 'categoria_id', 'nombre', 'genero'], 'modalidades_categoria_nombre_genero_unique');
        });

        DB::table('categorias')
            ->whereNotNull('modalidad_id')
            ->orderBy('id')
            ->chunkById(100, function ($categorias) {
                foreach ($categorias as $categoria) {
                    DB::table('modalidades')
                        ->where('id', $categoria->modalidad_id)
                        ->update(['categoria_id' => $categoria->id]);
                }
            });

        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn('modalidad_id');
        });

        Schema::table('inscripcion_competidor_modalidades', function (Blueprint $table) {
            $table->dropColumn('categoria_id');
            $table->dropIndex('icm_modalidad_id_index');
            $table->unique(['inscripcion_competidor_id', 'modalidad_id'], 'icm_competidor_modalidad_unique');
        });
    }
};
