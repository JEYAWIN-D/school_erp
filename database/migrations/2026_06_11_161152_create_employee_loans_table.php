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
        Schema::create('employee_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('loan_type')->default('personal'); // personal, vehicle, house, education
            $table->decimal('principal_amount', 12, 2);
            $table->decimal('interest_rate', 5, 2)->default(0); // annual %
            $table->integer('emi_months'); // loan tenure in months
            $table->decimal('emi_amount', 10, 2);
            $table->decimal('total_paid', 12, 2)->default(0);
            $table->decimal('outstanding_balance', 12, 2);
            $table->date('disbursement_date');
            $table->date('emi_start_month'); // first deduction month
            $table->string('status')->default('active'); // active, closed, paused
            $table->text('purpose')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_loans');
    }
};
