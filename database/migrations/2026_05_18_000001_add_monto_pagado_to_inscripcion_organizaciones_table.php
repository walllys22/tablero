<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inscripcion_organizaciones', function (Blueprint $table) {
            $table->decimal('monto_pagado', 10, 2)->default(0)->after('costo');
        });
    }

    public function down(): void
    {
        Schema::table('inscripcion_organizaciones', function (Blueprint $table) {
            $table->dropColumn('monto_pagado');
        });
    }
};
