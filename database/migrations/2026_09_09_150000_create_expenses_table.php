<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number', 64)->unique();
            $table->enum('category', ['academic', 'maintenance'])->index();
            $table->string('subcategory', 100)->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2);
            $table->date('expense_date')->index();
            $table->string('payment_method', 50)->default('bank_transfer');
            $table->string('vendor_name')->nullable();
            $table->string('vendor_invoice_no')->nullable();
            $table->string('invoice_receipt_path')->nullable();
            
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Multi-level MIS Approval
            // Academic: Coordinator -> Admin -> Principal
            // Maintenance: Admin -> Principal -> Correspondent
            $table->string('approval_status', 32)->default('pending')->index(); // pending, verified, approved, rejected
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
