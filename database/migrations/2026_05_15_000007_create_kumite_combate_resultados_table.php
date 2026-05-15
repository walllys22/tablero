<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kumite_combate_resultados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sorteo_llave_id')->constrained('sorteo_llaves')->cascadeOnDelete();
            $table->unsignedInteger('numero_llave');
            $table->unsignedInteger('indice_combate');
            $table->string('competidor_rojo')->nullable();
            $table->string('competidor_azul')->nullable();
            $table->unsignedSmallInteger('puntaje_rojo')->default(0);
            $table->unsignedSmallInteger('puntaje_azul')->default(0);
            $table->json('faltas_rojo')->nullable();
            $table->json('faltas_azul')->nullable();
            $table->string('senshu')->nullable();
            $table->json('tecnicas_rojo')->nullable();
            $table->json('tecnicas_azul')->nullable();
            $table->string('ganador')->nullable();
            $table->string('ganador_color')->nullable();
            $table->timestamp('realizado_at')->nullable();
            $table->timestamps();

            $table->unique(['sorteo_llave_id', 'indice_combate']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kumite_combate_resultados');
    }
};
