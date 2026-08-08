<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Online exam schedule
        if (!Schema::hasTable('online_exams')) {
            Schema::create('online_exams', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->unsignedBigInteger('academic_year_id')->nullable();
                $table->unsignedBigInteger('class_id');
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->dateTime('start_time');
                $table->dateTime('end_time');
                $table->unsignedInteger('duration_minutes');
                $table->decimal('total_marks', 8, 2)->default(0);
                $table->decimal('pass_marks', 8, 2)->default(0);
                $table->boolean('randomise_questions')->default(true);
                $table->boolean('negative_marking')->default(false);
                $table->decimal('negative_marks_per_wrong', 5, 2)->default(0);
                $table->enum('status', ['draft', 'published', 'ongoing', 'completed'])->default('draft');
                $table->text('instructions')->nullable();
                $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        // Questions assigned to an online exam (from question bank)
        if (!Schema::hasTable('online_exam_questions')) {
            Schema::create('online_exam_questions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('online_exam_id');
                $table->unsignedBigInteger('question_bank_id');
                $table->unsignedInteger('sort_order')->default(0);
                $table->foreign('online_exam_id')->references('id')->on('online_exams')->cascadeOnDelete();
                $table->foreign('question_bank_id')->references('id')->on('question_bank')->cascadeOnDelete();
                $table->unique(['online_exam_id', 'question_bank_id'], 'uniq_exam_question');
                $table->timestamps();
            });
        }

        // Student exam attempt (one per student per exam)
        if (!Schema::hasTable('online_exam_attempts')) {
            Schema::create('online_exam_attempts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('online_exam_id');
                $table->unsignedBigInteger('student_id');
                $table->dateTime('started_at')->nullable();
                $table->dateTime('submitted_at')->nullable();
                $table->boolean('auto_submitted')->default(false);
                $table->decimal('score', 8, 2)->nullable();
                $table->decimal('negative_marks', 8, 2)->default(0);
                $table->decimal('final_score', 8, 2)->nullable();
                $table->enum('result', ['pass', 'fail', 'pending'])->default('pending');
                $table->json('question_order')->nullable();
                $table->foreign('online_exam_id')->references('id')->on('online_exams')->cascadeOnDelete();
                $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
                $table->unique(['online_exam_id', 'student_id'], 'uniq_attempt');
                $table->timestamps();
            });
        }

        // Per-question responses
        if (!Schema::hasTable('online_exam_responses')) {
            Schema::create('online_exam_responses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('attempt_id');
                $table->unsignedBigInteger('question_bank_id');
                $table->string('answer')->nullable();
                $table->boolean('is_correct')->nullable();
                $table->decimal('marks_awarded', 6, 2)->default(0);
                $table->text('evaluator_remarks')->nullable();
                $table->unsignedBigInteger('evaluated_by')->nullable();
                $table->foreign('attempt_id')->references('id')->on('online_exam_attempts')->cascadeOnDelete();
                $table->foreign('question_bank_id')->references('id')->on('question_bank')->cascadeOnDelete();
                $table->unique(['attempt_id', 'question_bank_id'], 'uniq_response');
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('online_exam_responses');
        Schema::dropIfExists('online_exam_attempts');
        Schema::dropIfExists('online_exam_questions');
        Schema::dropIfExists('online_exams');
    }
};
