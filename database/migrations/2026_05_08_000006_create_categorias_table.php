<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneo_id')->constrained('torneos')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('genero', 30)->nullable();
            $table->unsignedTinyInteger('edad_desde')->nullable();
            $table->unsignedTinyInteger('edad_hasta')->nullable();
            $table->decimal('peso_desde', 6, 2)->nullable();
            $table->decimal('peso_hasta', 6, 2)->nullable();
            $table->string('grado', 100)->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
