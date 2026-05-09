<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('categorias', 'created_at') && ! Schema::hasColumn('categorias', 'updated_at')) {
            return;
        }

        Schema::table('categorias', function (Blueprint $table) {
            if (Schema::hasColumn('categorias', 'created_at')) {
                $table->dropColumn('created_at');
            }

            if (Schema::hasColumn('categorias', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('categorias', 'created_at') || Schema::hasColumn('categorias', 'updated_at')) {
            return;
        }

        Schema::table('categorias', function (Blueprint $table) {
            $table->timestamps();
        });
    }
};
