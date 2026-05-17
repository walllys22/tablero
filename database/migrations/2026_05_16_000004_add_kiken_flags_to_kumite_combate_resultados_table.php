<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kumite_combate_resultados', function (Blueprint $table) {
            $table->boolean('kiken_rojo')->default(false)->after('senshu_azul');
            $table->boolean('kiken_azul')->default(false)->after('kiken_rojo');
        });
    }

    public function down(): void
    {
        Schema::table('kumite_combate_resultados', function (Blueprint $table) {
            $table->dropColumn(['kiken_rojo', 'kiken_azul']);
        });
    }
};
