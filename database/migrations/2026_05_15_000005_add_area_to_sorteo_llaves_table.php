<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sorteo_llaves', function (Blueprint $table) {
            $table->unsignedSmallInteger('area')->nullable()->after('llaves');
        });
    }

    public function down(): void
    {
        Schema::table('sorteo_llaves', function (Blueprint $table) {
            $table->dropColumn('area');
        });
    }
};
