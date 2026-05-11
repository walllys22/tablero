<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arbitros', function (Blueprint $table) {
            $table->dropUnique(['torneo_id', 'persona_id']);
            $table->unique(
                ['torneo_id', 'persona_id', 'licencia_tipo_id', 'cargo', 'modalidad', 'rango'],
                'arbitros_unique_license_assignment'
            );
        });
    }

    public function down(): void
    {
        Schema::table('arbitros', function (Blueprint $table) {
            $table->dropUnique('arbitros_unique_license_assignment');
            $table->unique(['torneo_id', 'persona_id']);
        });
    }
};
