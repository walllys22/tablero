<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sorteo_llaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneo_id')->constrained('torneos')->cascadeOnDelete();
            $table->foreignId('modalidad_id')->constrained('modalidades')->cascadeOnDelete();
            $table->foreignId('categoria_id')->constrained('categorias')->cascadeOnDelete();
            $table->unsignedInteger('seed');
            $table->json('llaves');
            $table->timestamps();

            $table->unique(['torneo_id', 'categoria_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sorteo_llaves');
    }
};
