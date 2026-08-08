<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lms_courses')) {
            Schema::create('lms_courses', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->unsignedBigInteger('subject_id')->nullable()->index();
                $table->unsignedBigInteger('class_id')->nullable()->index();
                $table->text('description')->nullable();
                $table->string('thumbnail')->nullable();
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
                $table->unsignedBigInteger('created_by');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lms_units')) {
            Schema::create('lms_units', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('course_id')->index();
                $table->string('title');
                $table->unsignedInteger('order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lms_lessons')) {
            Schema::create('lms_lessons', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('unit_id')->index();
                $table->string('title');
                $table->enum('type', ['video', 'pdf', 'text', 'quiz', 'assignment'])->default('text');
                $table->longText('body')->nullable();       // for text type
                $table->string('video_url')->nullable();    // YouTube URL
                $table->string('file_path')->nullable();    // PDF upload path
                $table->unsignedInteger('order')->default(0);
                $table->boolean('is_published')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lms_lesson_progress')) {
            Schema::create('lms_lesson_progress', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id')->index();
                $table->unsignedBigInteger('lesson_id')->index();
                $table->timestamp('completed_at');
                $table->unique(['student_id', 'lesson_id']);
            });
        }

        if (!Schema::hasTable('lms_quizzes')) {
            Schema::create('lms_quizzes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('lesson_id')->nullable()->index();
                $table->unsignedBigInteger('course_id')->index();
                $table->string('title');
                $table->unsignedInteger('duration_minutes')->default(30);
                $table->decimal('marks_per_question', 5, 2)->default(1);
                $table->decimal('negative_marks', 5, 2)->default(0);
                $table->timestamp('available_from')->nullable();
                $table->timestamp('available_to')->nullable();
                $table->boolean('randomise')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lms_quiz_questions')) {
            Schema::create('lms_quiz_questions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('quiz_id')->index();
                $table->text('question');
                $table->enum('type', ['mcq_single', 'mcq_multi', 'true_false', 'fill_blank'])->default('mcq_single');
                $table->json('options')->nullable();        // array of option strings for MCQ
                $table->text('correct_answer');             // for MCQ: option index(es); for fill_blank: the answer
                $table->decimal('marks', 5, 2)->default(1);
                $table->unsignedInteger('order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lms_quiz_attempts')) {
            Schema::create('lms_quiz_attempts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('quiz_id')->index();
                $table->unsignedBigInteger('student_id')->index();
                $table->decimal('score', 7, 2)->default(0);
                $table->decimal('total_marks', 7, 2)->default(0);
                $table->timestamp('started_at');
                $table->timestamp('submitted_at')->nullable();
                $table->enum('status', ['in_progress', 'submitted', 'evaluated'])->default('in_progress');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lms_quiz_answers')) {
            Schema::create('lms_quiz_answers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('attempt_id')->index();
                $table->unsignedBigInteger('question_id')->index();
                $table->text('given_answer')->nullable();
                $table->boolean('is_correct')->nullable();
                $table->decimal('marks_awarded', 5, 2)->default(0);
            });
        }

        if (!Schema::hasTable('lms_discussions')) {
            Schema::create('lms_discussions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('course_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('title');
                $table->text('body');
                $table->boolean('is_answered')->default(false);
                $table->boolean('is_hidden')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lms_discussion_replies')) {
            Schema::create('lms_discussion_replies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('discussion_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->text('body');
                $table->boolean('is_answer')->default(false);   // teacher marks as accepted answer
                $table->boolean('is_hidden')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lms_assignments')) {
            Schema::create('lms_assignments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('course_id')->index();
                $table->unsignedBigInteger('lesson_id')->nullable()->index();
                $table->string('title');
                $table->text('instructions');
                $table->timestamp('due_at');
                $table->decimal('max_marks', 7, 2)->default(10);
                $table->string('attachment')->nullable();
                $table->unsignedBigInteger('created_by');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lms_assignment_submissions')) {
            Schema::create('lms_assignment_submissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('assignment_id')->index();
                $table->unsignedBigInteger('student_id')->index();
                $table->string('file_path')->nullable();
                $table->text('note')->nullable();
                $table->timestamp('submitted_at');
                $table->boolean('is_late')->default(false);
                $table->decimal('score', 7, 2)->nullable();
                $table->text('feedback')->nullable();
                $table->timestamp('evaluated_at')->nullable();
                $table->unsignedBigInteger('evaluated_by')->nullable();
                $table->unique(['assignment_id', 'student_id']);
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'lms_assignment_submissions', 'lms_assignments',
            'lms_discussion_replies', 'lms_discussions',
            'lms_quiz_answers', 'lms_quiz_attempts', 'lms_quiz_questions', 'lms_quizzes',
            'lms_lesson_progress', 'lms_lessons', 'lms_units', 'lms_courses',
        ];
        foreach ($tables as $t) {
            Schema::dropIfExists($t);
        }
    }
};
