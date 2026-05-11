<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('organizaciones', 'estilo_id')) {
            Schema::table('organizaciones', function (Blueprint $table) {
                $table->foreignId('estilo_id')->nullable()->after('persona_id')->constrained('estiloskarate')->nullOnDelete();
            });
        }

        if (Schema::hasColumn('organizaciones', 'estilo') || Schema::hasColumn('organizaciones', 'lineas')) {
            Schema::table('organizaciones', function (Blueprint $table) {
                if (Schema::hasColumn('organizaciones', 'estilo')) {
                    $table->dropColumn('estilo');
                }
                if (Schema::hasColumn('organizaciones', 'lineas')) {
                    $table->dropColumn('lineas');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('organizaciones', 'estilo_id')) {
            Schema::table('organizaciones', function (Blueprint $table) {
                $table->dropConstrainedForeignId('estilo_id');
            });
        }

        Schema::table('organizaciones', function (Blueprint $table) {
            if (!Schema::hasColumn('organizaciones', 'estilo')) {
                $table->string('estilo')->nullable()->after('nombre');
            }
            if (!Schema::hasColumn('organizaciones', 'lineas')) {
                $table->string('lineas')->nullable()->after('estilo');
            }
        });
    }
};
