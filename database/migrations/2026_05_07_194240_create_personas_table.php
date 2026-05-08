<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string('ci')->nullable()->index();
            $table->string('first_name')->index();
            $table->date('birth_date')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('country_code', 10)->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('gender', 50)->nullable();
            $table->string('sangre', 20)->nullable();
            $table->string('image', 600)->nullable();
            $table->boolean('status')->default(true)->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
