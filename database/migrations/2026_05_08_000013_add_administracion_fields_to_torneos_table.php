<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('torneos', function (Blueprint $table) {
            $table->string('ciudad')->nullable()->after('nombre')->index();
            $table->text('direccion')->nullable()->after('ciudad');
            $table->string('sistema_competencia', 50)->default('tradicional')->after('direccion');
            $table->string('modalidad_puntaje', 100)->nullable()->after('sistema_competencia');
            $table->string('organiza')->nullable()->after('modalidad_puntaje');
        });
    }

    public function down(): void
    {
        Schema::table('torneos', function (Blueprint $table) {
            $table->dropColumn([
                'ciudad',
                'direccion',
                'sistema_competencia',
                'modalidad_puntaje',
                'organiza',
            ]);
        });
    }
};
