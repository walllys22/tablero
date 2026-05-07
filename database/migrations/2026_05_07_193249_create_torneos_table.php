<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('torneos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable(); 
            $table->string('nombre')->nullable();
            $table->string('lugar')->nullable();
            $table->string('logo',600)->nullable();
            $table->smallInteger('status')->default(1); // 0: Inactivo, 1: activo

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('torneos');
    }
};
