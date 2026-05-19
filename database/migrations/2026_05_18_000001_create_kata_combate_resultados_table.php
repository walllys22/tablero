<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kata_combate_resultados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sorteo_llave_id')->nullable()->constrained('sorteo_llaves')->nullOnDelete();
            $table->unsignedInteger('indice_combate')->default(0);
            $table->string('competidor_rojo')->nullable();
            $table->string('competidor_azul')->nullable();
            $table->string('kata_numero_rojo')->nullable();
            $table->string('kata_numero_azul')->nullable();
            $table->string('kata_nombre_rojo')->nullable();
            $table->string('kata_nombre_azul')->nullable();
            $table->unsignedSmallInteger('puntaje_rojo')->default(0);
            $table->unsignedSmallInteger('puntaje_azul')->default(0);
            $table->boolean('kiken_rojo')->default(false);
            $table->boolean('kiken_azul')->default(false);
            $table->string('ganador')->nullable();
            $table->string('ganador_color')->nullable();
            $table->timestamp('realizado_at')->nullable();
            $table->timestamps();

            $table->unique(['sorteo_llave_id', 'indice_combate']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kata_combate_resultados');
    }
};
