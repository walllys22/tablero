<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('sorteo_llaves', 'orden')) {
            return;
        }

        Schema::table('sorteo_llaves', function (Blueprint $table) {
            $table->unsignedInteger('orden')->nullable()->after('area');
        });

        DB::table('sorteo_llaves')
            ->orderBy('torneo_id')
            ->orderBy('id')
            ->get(['id', 'torneo_id'])
            ->groupBy('torneo_id')
            ->each(function ($sorteos) {
                foreach ($sorteos->values() as $index => $sorteo) {
                    DB::table('sorteo_llaves')
                        ->where('id', $sorteo->id)
                        ->update(['orden' => $index + 1]);
                }
            });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('sorteo_llaves', 'orden')) {
            return;
        }

        Schema::table('sorteo_llaves', function (Blueprint $table) {
            $table->dropColumn('orden');
        });
    }
};
