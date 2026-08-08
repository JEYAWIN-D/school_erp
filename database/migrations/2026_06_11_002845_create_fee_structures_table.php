<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fee_heads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique()->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('fee_head_id');
            $table->decimal('amount', 10, 2);
            $table->enum('frequency', ['one_time','monthly','quarterly','half_yearly','annually'])->default('annually');
            $table->date('due_date')->nullable();
            $table->decimal('late_fee_per_day', 8, 2)->default(0);
            $table->boolean('is_mandatory')->default(true);
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete();
            $table->foreign('fee_head_id')->references('id')->on('fee_heads')->cascadeOnDelete();
            $table->unique(['academic_year_id', 'class_id', 'fee_head_id']);
            $table->timestamps();
        });

        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('fee_head_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('late_fee', 8, 2)->default(0);
            $table->decimal('discount', 8, 2)->default(0);
            $table->decimal('total_paid', 10, 2);
            $table->enum('payment_mode', ['cash','cheque','dd','online','upi'])->default('cash');
            $table->string('transaction_id')->nullable();
            $table->string('cheque_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->date('payment_date');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('collected_by')->nullable();
            $table->boolean('is_cancelled')->default(false);
            $table->text('cancel_reason')->nullable();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            $table->foreign('fee_head_id')->references('id')->on('fee_heads')->nullOnDelete();
            $table->foreign('collected_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['student_id', 'academic_year_id']);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('fee_payments');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('fee_heads');
    }
};
