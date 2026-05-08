<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modalidades', function (Blueprint $table) {
            $table->index('torneo_id', 'modalidades_torneo_id_index');
            $table->dropUnique('modalidades_torneo_id_nombre_genero_unique');
            $table->unique(['torneo_id', 'categoria_id', 'nombre', 'genero'], 'modalidades_categoria_nombre_genero_unique');
        });
    }

    public function down(): void
    {
        Schema::table('modalidades', function (Blueprint $table) {
            $table->dropUnique('modalidades_categoria_nombre_genero_unique');
            $table->unique(['torneo_id', 'nombre', 'genero'], 'modalidades_torneo_id_nombre_genero_unique');
            $table->dropIndex('modalidades_torneo_id_index');
        });
    }
};
