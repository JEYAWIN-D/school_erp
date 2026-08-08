<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('student_fee_charges')) {
            Schema::create('student_fee_charges', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('academic_year_id')->nullable();
                $table->unsignedBigInteger('fee_head_id')->nullable();
                $table->decimal('amount', 10, 2);
                $table->date('due_date')->nullable();
                $table->string('description')->nullable();
                $table->string('source')->nullable(); // hostel_allotment, manual
                $table->unsignedBigInteger('source_id')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
                $table->foreign('fee_head_id')->references('id')->on('fee_heads')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('student_fee_charges');
    }
};
