<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('enquiry_number')->unique();
            $table->string('student_name');
            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->foreign('class_id')->references('id')->on('classes')->nullOnDelete();
            $table->string('parent_name');
            $table->string('parent_mobile', 15);
            $table->string('parent_email')->nullable();
            $table->string('address')->nullable();
            $table->string('source')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['new', 'follow_up', 'converted', 'lost'])->default('new');
            $table->date('follow_up_date')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->nullOnDelete();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->string('previous_school')->nullable();
            $table->string('previous_class')->nullable();
            $table->decimal('previous_percentage', 5, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'academic_year_id']);
            $table->index('parent_mobile');
        });

        Schema::create('enquiry_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enquiry_id');
            $table->foreign('enquiry_id')->references('id')->on('enquiries')->cascadeOnDelete();
            $table->text('notes');
            $table->date('next_follow_up_date')->nullable();
            $table->enum('status', ['new', 'follow_up', 'converted', 'lost']);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiry_follow_ups');
        Schema::dropIfExists('enquiries');
    }
};
