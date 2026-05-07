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
            $table->string('ci')->nullable();
            $table->string('first_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('email')->nullable();
            $table->string('country_code')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('gender')->nullable();
            $table->string('sangre')->nullable();
            $table->string('image',600)->nullable();      
            $table->smallInteger('status')->default(1);
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
