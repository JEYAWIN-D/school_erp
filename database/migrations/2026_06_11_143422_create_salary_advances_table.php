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
        if (!Schema::hasTable('salary_advances')) {
            Schema::create('salary_advances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
                $table->decimal('amount', 10, 2);
                $table->date('advance_date');
                $table->string('reason')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected', 'recovered'])->default('pending');
                $table->unsignedTinyInteger('recovery_months')->default(1)->comment('Over how many months to recover');
                $table->decimal('monthly_deduction', 10, 2)->default(0);
                $table->decimal('recovered_amount', 10, 2)->default(0);
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->date('approved_date')->nullable();
                $table->string('remarks')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_advances');
    }
};
