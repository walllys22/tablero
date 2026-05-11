<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $defaultSistemaId = DB::table('sistema_competencia')->orderBy('id')->value('id');

        if (! $defaultSistemaId) {
            $defaultSistemaId = DB::table('sistema_competencia')->insertGetId([
                'nombre' => 'WKF',
                'estado' => 'Activo',
            ]);
        }

        $wkfSistemaId = DB::table('sistema_competencia')
            ->whereRaw('LOWER(nombre) = ?', ['wkf'])
            ->value('id') ?: $defaultSistemaId;

        $shorinkanSistemaId = DB::table('sistema_competencia')
            ->whereRaw('LOWER(nombre) = ?', ['shorinkan'])
            ->value('id') ?: $defaultSistemaId;

        DB::table('torneos')
            ->whereRaw('LOWER(sistema_competencia) = ?', ['wkf'])
            ->update(['sistema_competencia' => (string) $wkfSistemaId]);

        DB::table('torneos')
            ->whereRaw('LOWER(sistema_competencia) = ?', ['shorinkan'])
            ->update(['sistema_competencia' => (string) $shorinkanSistemaId]);

        DB::table('torneos')
            ->where(function ($query) use ($defaultSistemaId) {
                $query->whereNull('sistema_competencia')
                    ->orWhere('sistema_competencia', '')
                    ->orWhereRaw('sistema_competencia NOT REGEXP "^[0-9]+$"')
                    ->orWhereNotIn('sistema_competencia', function ($query) {
                        $query->selectRaw('CAST(id AS CHAR)')->from('sistema_competencia');
                    });
            })
            ->update(['sistema_competencia' => (string) $defaultSistemaId]);

        DB::statement('ALTER TABLE torneos MODIFY sistema_competencia BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE torneos ADD CONSTRAINT torneos_sistema_competencia_foreign FOREIGN KEY (sistema_competencia) REFERENCES sistema_competencia(id) ON DELETE RESTRICT');
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('ALTER TABLE torneos DROP FOREIGN KEY torneos_sistema_competencia_foreign');
        Schema::enableForeignKeyConstraints();

        DB::statement('ALTER TABLE torneos MODIFY sistema_competencia VARCHAR(50) NOT NULL DEFAULT "tradicional"');
    }
};
