<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripcion_organizaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneo_id')->constrained('torneos')->cascadeOnDelete();
            $table->foreignId('organizacion_id')->constrained('organizaciones')->cascadeOnDelete();
            $table->decimal('costo', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['torneo_id', 'organizacion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripcion_organizaciones');
    }
};
