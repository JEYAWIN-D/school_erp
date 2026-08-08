<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Competency master per subject per class
        if (!Schema::hasTable('competencies')) {
            Schema::create('competencies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('subject_id');
                $table->unsignedBigInteger('class_id');
                $table->string('name');
                $table->string('code', 30)->nullable();
                $table->text('description')->nullable();
                $table->enum('domain', ['cognitive', 'affective', 'psychomotor', 'co_scholastic'])->default('cognitive');
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete();
                $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        // Map competencies to topics/chapters in syllabus
        if (!Schema::hasTable('competency_topic_map')) {
            Schema::create('competency_topic_map', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('competency_id');
                $table->unsignedBigInteger('syllabus_topic_id')->nullable();
                $table->string('topic_name')->nullable();
                $table->foreign('competency_id')->references('id')->on('competencies')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        // Competency assessment entries per student
        if (!Schema::hasTable('competency_assessments')) {
            Schema::create('competency_assessments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('competency_id');
                $table->unsignedBigInteger('academic_year_id');
                $table->string('term')->nullable(); // Term 1, Term 2, Annual
                $table->enum('level', ['achieved', 'partially_achieved', 'not_achieved', 'not_assessed'])->default('not_assessed');
                $table->text('remarks')->nullable();
                $table->unsignedBigInteger('assessed_by')->nullable();
                $table->date('assessment_date')->nullable();
                $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
                $table->foreign('competency_id')->references('id')->on('competencies')->cascadeOnDelete();
                $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
                $table->unique(['student_id', 'competency_id', 'academic_year_id', 'term'], 'uniq_comp_assessment');
                $table->timestamps();
            });
        }

        // 360-degree / co-scholastic assessment (arts, sports, values)
        if (!Schema::hasTable('coscholastic_assessments')) {
            Schema::create('coscholastic_assessments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('academic_year_id');
                $table->string('term')->nullable();
                $table->string('area'); // arts, sports, values, health_hygiene, work_education
                $table->string('sub_area')->nullable(); // e.g. painting, football, honesty
                $table->enum('grade', ['A', 'B', 'C', 'D'])->default('A');
                $table->text('remarks')->nullable();
                $table->unsignedBigInteger('assessed_by')->nullable();
                $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
                $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        // Activity-based learning records
        if (!Schema::hasTable('activity_learning_records')) {
            Schema::create('activity_learning_records', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('academic_year_id');
                $table->string('activity_name');
                $table->string('activity_type'); // project, experiment, field_trip, art, sports, community_service
                $table->text('description')->nullable();
                $table->date('activity_date');
                $table->string('outcome')->nullable();
                $table->string('attachment')->nullable();
                $table->unsignedBigInteger('recorded_by')->nullable();
                $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
                $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('activity_learning_records');
        Schema::dropIfExists('coscholastic_assessments');
        Schema::dropIfExists('competency_assessments');
        Schema::dropIfExists('competency_topic_map');
        Schema::dropIfExists('competencies');
    }
};
