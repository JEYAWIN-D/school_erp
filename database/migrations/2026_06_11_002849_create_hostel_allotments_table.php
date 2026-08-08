<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('hostel_allotments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->date('allotment_date');
            $table->date('vacating_date')->nullable();
            $table->enum('status', ['active', 'vacated'])->default('active');
            $table->decimal('monthly_fee', 8, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->foreign('room_id')->references('id')->on('hostel_rooms')->cascadeOnDelete();
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            $table->unique(['student_id', 'academic_year_id']);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('hostel_allotments'); }
};
