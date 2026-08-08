<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('question_papers')) {
            Schema::create('question_papers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('exam_id')->nullable();
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->unsignedBigInteger('class_id')->nullable();
                $table->string('title');
                $table->string('file_path');
                $table->string('original_name')->nullable();
                $table->date('accessible_from')->nullable();
                $table->boolean('is_restricted')->default(true);
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->timestamps();

                $table->foreign('exam_id')->references('id')->on('exams')->nullOnDelete();
                $table->foreign('subject_id')->references('id')->on('subjects')->nullOnDelete();
                $table->foreign('class_id')->references('id')->on('classes')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('question_papers');
    }
};
