<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('katas', function ($table) {
            $table->dropUnique('katas_nombre_unique');
        });

        $duplicados = DB::table('katas')
            ->select('sistema_id', 'nombre')
            ->groupBy('sistema_id', 'nombre')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if (! $duplicados) {
            Schema::table('katas', function ($table) {
                $table->unique(['sistema_id', 'nombre'], 'katas_sistema_nombre_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('katas', function ($table) {
            $table->dropUnique('katas_sistema_nombre_unique');
        });

        Schema::table('katas', function ($table) {
            $table->unique('nombre', 'katas_nombre_unique');
        });
    }
};
