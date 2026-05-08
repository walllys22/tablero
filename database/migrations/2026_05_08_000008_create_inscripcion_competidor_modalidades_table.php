<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('inscripcion_competidor_modalidades');

        Schema::create('inscripcion_competidor_modalidades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inscripcion_competidor_id');
            $table->unsignedBigInteger('modalidad_id');
            $table->decimal('costo', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('inscripcion_competidor_id', 'icm_competidor_fk')
                ->references('id')
                ->on('inscripcion_competidores')
                ->cascadeOnDelete();
            $table->foreign('modalidad_id', 'icm_modalidad_fk')
                ->references('id')
                ->on('modalidades')
                ->cascadeOnDelete();
            $table->unique(['inscripcion_competidor_id', 'modalidad_id'], 'icm_competidor_modalidad_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripcion_competidor_modalidades');
    }
};
