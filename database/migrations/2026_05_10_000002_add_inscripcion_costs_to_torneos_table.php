<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('torneos', function (Blueprint $table) {
            $table->decimal('costo_inscripcion_organizacion', 10, 2)->default(0)->after('modalidad_puntaje');
            $table->decimal('costo_inscripcion_competidor', 10, 2)->default(0)->after('costo_inscripcion_organizacion');
        });
    }

    public function down(): void
    {
        Schema::table('torneos', function (Blueprint $table) {
            $table->dropColumn(['costo_inscripcion_organizacion', 'costo_inscripcion_competidor']);
        });
    }
};
