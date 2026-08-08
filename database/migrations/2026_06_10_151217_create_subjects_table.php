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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->nullable();
            $table->enum('type', ['theory', 'practical', 'activity', 'language'])->default('theory');
            $table->enum('language_level', ['first', 'second', 'third'])->nullable();
            $table->boolean('is_elective')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('credit_hours')->default(1);
            $table->string('medium')->default('English');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
