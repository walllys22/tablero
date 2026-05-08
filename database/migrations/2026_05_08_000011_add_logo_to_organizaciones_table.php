<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizaciones', function (Blueprint $table) {
            $table->string('logo', 600)->nullable()->after('persona_id');
        });
    }

    public function down(): void
    {
        Schema::table('organizaciones', function (Blueprint $table) {
            $table->dropColumn('logo');
        });
    }
};
