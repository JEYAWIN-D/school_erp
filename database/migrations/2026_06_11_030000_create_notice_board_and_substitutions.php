<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('notice_type')->default('general'); // general, circular, academic, exam, fee, event
            $table->string('target_audience')->default('all'); // all, students, staff, parents, class_specific
            $table->unsignedBigInteger('target_class_id')->nullable();
            $table->date('publish_date');
            $table->date('expiry_date')->nullable();
            $table->string('attachment')->nullable();
            $table->boolean('is_published')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('notice_reads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notice_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('read_at');
            $table->unique(['notice_id', 'user_id']);
            $table->foreign('notice_id')->references('id')->on('notices')->cascadeOnDelete();
        });

        Schema::create('substitutions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('absent_teacher_id');
            $table->unsignedBigInteger('substitute_teacher_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedInteger('period_number');
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('arranged_by');
            $table->timestamps();
            $table->foreign('absent_teacher_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->foreign('substitute_teacher_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('substitutions');
        Schema::dropIfExists('notice_reads');
        Schema::dropIfExists('notices');
    }
};
