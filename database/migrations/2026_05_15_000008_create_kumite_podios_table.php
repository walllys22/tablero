<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kumite_podios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sorteo_llave_id')->constrained('sorteo_llaves')->cascadeOnDelete();
            $table->string('oro')->nullable();
            $table->string('plata')->nullable();
            $table->string('bronce_1')->nullable();
            $table->string('bronce_2')->nullable();
            $table->timestamp('generado_at')->nullable();
            $table->timestamps();

            $table->unique('sorteo_llave_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kumite_podios');
    }
};
