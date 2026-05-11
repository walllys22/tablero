<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('katas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->foreignId('sistema_id')->constrained('sistema_competencia')->restrictOnDelete();
            $table->string('estado')->default('Activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('katas');
    }
};
