<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicados = DB::table('katas')
            ->select('numero')
            ->whereNotNull('numero')
            ->groupBy('numero')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if (! $duplicados) {
            Schema::table('katas', function ($table) {
                $table->unique('numero', 'katas_numero_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('katas', function ($table) {
            $table->dropUnique('katas_numero_unique');
        });
    }
};
