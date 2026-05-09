<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arbitros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneo_id')->constrained('torneos')->cascadeOnDelete();
            $table->foreignId('persona_id')->constrained('personas')->cascadeOnDelete();
            $table->string('cargo', 30);
            $table->string('modalidad', 30);
            $table->string('rango', 5);
            $table->foreignId('licencia_tipo_id')->constrained('licencia_tipos')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['torneo_id', 'persona_id']);
            $table->index(['torneo_id', 'cargo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arbitros');
    }
};
