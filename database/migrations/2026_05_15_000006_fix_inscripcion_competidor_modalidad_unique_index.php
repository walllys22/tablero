<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            $this->dropMysqlIndexIfExists('inscripcion_competidor_modalidades', 'icm_competidor_modalidad_unique');

            if (! $this->mysqlIndexExists('inscripcion_competidor_modalidades', 'icm_competidor_modalidad_categoria_unique')) {
                Schema::table('inscripcion_competidor_modalidades', function (Blueprint $table) {
                    $table->unique(
                        ['inscripcion_competidor_id', 'modalidad_id', 'categoria_id'],
                        'icm_competidor_modalidad_categoria_unique'
                    );
                });
            }

            return;
        }

        try {
            Schema::table('inscripcion_competidor_modalidades', function (Blueprint $table) {
                $table->dropUnique('icm_competidor_modalidad_unique');
            });
        } catch (Throwable $e) {
            //
        }

        try {
            Schema::table('inscripcion_competidor_modalidades', function (Blueprint $table) {
                $table->unique(
                    ['inscripcion_competidor_id', 'modalidad_id', 'categoria_id'],
                    'icm_competidor_modalidad_categoria_unique'
                );
            });
        } catch (Throwable $e) {
            //
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            $this->dropMysqlIndexIfExists('inscripcion_competidor_modalidades', 'icm_competidor_modalidad_categoria_unique');

            if (! $this->mysqlIndexExists('inscripcion_competidor_modalidades', 'icm_competidor_modalidad_unique')) {
                Schema::table('inscripcion_competidor_modalidades', function (Blueprint $table) {
                    $table->unique(
                        ['inscripcion_competidor_id', 'modalidad_id'],
                        'icm_competidor_modalidad_unique'
                    );
                });
            }

            return;
        }

        try {
            Schema::table('inscripcion_competidor_modalidades', function (Blueprint $table) {
                $table->dropUnique('icm_competidor_modalidad_categoria_unique');
            });
        } catch (Throwable $e) {
            //
        }

        try {
            Schema::table('inscripcion_competidor_modalidades', function (Blueprint $table) {
                $table->unique(['inscripcion_competidor_id', 'modalidad_id'], 'icm_competidor_modalidad_unique');
            });
        } catch (Throwable $e) {
            //
        }
    }

    private function mysqlIndexExists(string $table, string $index): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->exists();
    }

    private function dropMysqlIndexIfExists(string $table, string $index): void
    {
        if ($this->mysqlIndexExists($table, $index)) {
            DB::statement("ALTER TABLE {$table} DROP INDEX {$index}");
        }
    }
};
