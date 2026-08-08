<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('overtime_entries')) {
            Schema::create('overtime_entries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id');
                $table->date('entry_date');
                $table->decimal('hours', 5, 2);
                $table->decimal('rate_per_hour', 8, 2)->default(0);
                $table->decimal('amount', 10, 2)->default(0);
                $table->string('month', 7); // YYYY-MM
                $table->string('remarks')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('arrears_bonus_entries')) {
            Schema::create('arrears_bonus_entries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id');
                $table->enum('type', ['arrears', 'bonus', 'other_addition'])->default('bonus');
                $table->decimal('amount', 10, 2);
                $table->string('month', 7); // YYYY-MM
                $table->string('description')->nullable();
                $table->string('payment_mode', 30)->default('payroll');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            });
        }

        // Add overtime and arrears columns to payroll_records
        Schema::table('payroll_records', function (Blueprint $table) {
            if (!Schema::hasColumn('payroll_records', 'overtime_amount')) {
                $table->decimal('overtime_amount', 10, 2)->default(0)->after('gross_salary');
            }
            if (!Schema::hasColumn('payroll_records', 'arrears_amount')) {
                $table->decimal('arrears_amount', 10, 2)->default(0)->after('overtime_amount');
            }
            if (!Schema::hasColumn('payroll_records', 'bonus_amount')) {
                $table->decimal('bonus_amount', 10, 2)->default(0)->after('arrears_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arrears_bonus_entries');
        Schema::dropIfExists('overtime_entries');
        Schema::table('payroll_records', function (Blueprint $table) {
            $table->dropColumn(['overtime_amount', 'arrears_amount', 'bonus_amount']);
        });
    }
};
