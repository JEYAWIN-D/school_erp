<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('question_paper_templates')) {
            Schema::create('question_paper_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('exam_type')->nullable(); // annual, unit_test, midterm, etc.
                $table->boolean('show_school_logo')->default(true);
                $table->boolean('show_school_name')->default(true);
                $table->boolean('show_school_address')->default(true);
                $table->boolean('show_affiliation')->default(true);
                $table->text('header_instructions')->nullable();
                $table->text('general_instructions')->nullable();
                $table->string('watermark_text')->nullable(); // CONFIDENTIAL etc.
                $table->enum('question_numbering', ['numeric','alpha','roman'])->default('numeric');
                $table->boolean('show_marks_per_question')->default(true);
                $table->boolean('show_section_totals')->default(true);
                $table->boolean('show_answer_lines')->default(false);
                $table->integer('answer_lines_count')->default(5);
                $table->string('paper_size', 10)->default('A4');
                $table->string('font_size', 10)->default('12');
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }
    }
    public function down(): void { Schema::dropIfExists('question_paper_templates'); }
};
