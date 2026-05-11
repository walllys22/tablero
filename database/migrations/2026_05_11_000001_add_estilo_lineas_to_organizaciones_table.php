<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizaciones', function (Blueprint $table) {
            $table->string('estilo')->nullable()->after('nombre');
            $table->string('lineas')->nullable()->after('estilo');
        });
    }

    public function down(): void
    {
        Schema::table('organizaciones', function (Blueprint $table) {
            $table->dropColumn(['estilo', 'lineas']);
        });
    }
};
