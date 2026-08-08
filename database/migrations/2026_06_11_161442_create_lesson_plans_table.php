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
        if (Schema::hasTable('lesson_plans')) { return; }
        Schema::create('lesson_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('syllabus_id')->nullable();
            $table->foreign('syllabus_id')->references('id')->on('syllabus')->nullOnDelete();
            $table->foreignId('class_id')->constrained('classes');
            $table->foreignId('subject_id')->constrained('subjects');
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('topic');
            $table->text('learning_objectives')->nullable();
            $table->text('teaching_method')->nullable();
            $table->text('resources_required')->nullable();
            $table->string('period_number')->nullable();
            $table->date('plan_date');
            $table->integer('duration_minutes')->default(45);
            $table->text('teacher_notes')->nullable();
            $table->string('status')->default('draft'); // draft, submitted, approved, rejected
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('hod_reviewed_by')->nullable()->constrained('users');
            $table->timestamp('hod_reviewed_at')->nullable();
            $table->text('hod_remarks')->nullable();
            $table->string('hod_decision')->nullable(); // approved, rejected
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_plans');
    }
};
