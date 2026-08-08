<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_year_id');
            $table->string('name');
            $table->enum('type', ['unit_test', 'term', 'final', 'pre_board', 'practice'])->default('term');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('passing_percentage', 5, 2)->default(33.00);
            $table->boolean('is_published')->default(false);
            $table->boolean('result_published')->default(false);
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('subject_id');
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('total_marks');
            $table->integer('passing_marks');
            $table->string('room')->nullable();
            $table->foreign('exam_id')->references('id')->on('exams')->cascadeOnDelete();
            $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete();
            $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete();
            $table->unique(['exam_id', 'class_id', 'subject_id']);
            $table->timestamps();
        });

        Schema::create('exam_marks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_schedule_id');
            $table->unsignedBigInteger('student_id');
            $table->decimal('marks_obtained', 6, 2)->nullable();
            $table->boolean('is_absent')->default(false);
            $table->string('grade', 5)->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('entered_by')->nullable();
            $table->foreign('exam_schedule_id')->references('id')->on('exam_schedules')->cascadeOnDelete();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->foreign('entered_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['exam_schedule_id', 'student_id']);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('exam_marks');
        Schema::dropIfExists('exam_schedules');
        Schema::dropIfExists('exams');
    }
};
