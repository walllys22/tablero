<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modalidades', function (Blueprint $table) {
            $table->foreignId('categoria_id')
                ->nullable()
                ->after('torneo_id')
                ->constrained('categorias', 'id', 'modalidades_categoria_fk')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('modalidades', function (Blueprint $table) {
            $table->dropForeign('modalidades_categoria_fk');
            $table->dropColumn('categoria_id');
        });
    }
};
