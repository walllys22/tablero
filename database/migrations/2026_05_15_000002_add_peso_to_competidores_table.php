<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competidores', function (Blueprint $table) {
            $table->decimal('peso', 8, 3)->nullable()->after('persona_id');
        });
    }

    public function down(): void
    {
        Schema::table('competidores', function (Blueprint $table) {
            $table->dropColumn('peso');
        });
    }
};
