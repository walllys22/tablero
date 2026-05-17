<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kumite_combate_resultados', function (Blueprint $table) {
            $table->boolean('senshu_rojo')->default(false)->after('senshu');
            $table->boolean('senshu_azul')->default(false)->after('senshu_rojo');
        });
    }

    public function down(): void
    {
        Schema::table('kumite_combate_resultados', function (Blueprint $table) {
            $table->dropColumn(['senshu_rojo', 'senshu_azul']);
        });
    }
};
