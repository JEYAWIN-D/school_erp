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
        if (!Schema::hasTable('homework_submissions')) {
            Schema::create('homework_submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('homework_id')->constrained()->cascadeOnDelete();
                $table->foreignId('student_id')->constrained()->cascadeOnDelete();
                $table->enum('status', ['submitted', 'late', 'not_submitted', 'evaluated'])->default('submitted');
                $table->date('submitted_at')->nullable();
                $table->string('file_path')->nullable();
                $table->text('student_remarks')->nullable();
                $table->decimal('score', 5, 2)->nullable();
                $table->text('teacher_feedback')->nullable();
                $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('evaluated_at')->nullable();
                $table->timestamps();
                $table->unique(['homework_id', 'student_id']);
            });
        }

        Schema::table('homework', function (Blueprint $table) {
            if (!Schema::hasColumn('homework', 'title')) {
                $table->string('title')->nullable()->after('subject_id');
            }
            if (!Schema::hasColumn('homework', 'max_score')) {
                $table->decimal('max_score', 5, 2)->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homework_submissions');
    }
};
