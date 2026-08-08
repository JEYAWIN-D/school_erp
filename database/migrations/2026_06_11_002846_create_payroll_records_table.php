<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payroll_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('month', 7); // YYYY-MM
            $table->integer('working_days')->default(26);
            $table->integer('present_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('leave_days')->default(0);
            $table->decimal('gross_salary', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2)->default(0);
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->date('paid_on')->nullable();
            $table->enum('payment_mode', ['cash', 'bank_transfer', 'cheque'])->nullable();
            $table->string('bank_reference')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['employee_id', 'month']);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('payroll_records'); }
};
