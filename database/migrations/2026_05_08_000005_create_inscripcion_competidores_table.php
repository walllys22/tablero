<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripcion_competidores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneo_id')->constrained('torneos')->cascadeOnDelete();
            $table->foreignId('inscripcion_organizacion_id')->constrained('inscripcion_organizaciones')->cascadeOnDelete();
            $table->foreignId('persona_id')->constrained('personas')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['torneo_id', 'persona_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripcion_competidores');
    }
};
