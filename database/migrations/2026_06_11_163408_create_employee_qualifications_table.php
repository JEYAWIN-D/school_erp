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
        if (Schema::hasTable('employee_qualifications')) return;
        Schema::create('employee_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('degree');
            $table->string('subject')->nullable();
            $table->string('institution');
            $table->string('university')->nullable();
            $table->year('year_of_passing')->nullable();
            $table->string('grade_or_percentage')->nullable();
            $table->enum('education_level', ['secondary','higher_secondary','diploma','graduate','post_graduate','doctorate','other'])->default('graduate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_qualifications');
    }
};
