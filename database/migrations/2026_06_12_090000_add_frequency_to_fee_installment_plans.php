<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fee_installment_plans')) {
            Schema::table('fee_installment_plans', function (Blueprint $table) {
                if (!Schema::hasColumn('fee_installment_plans', 'frequency')) {
                    $table->enum('frequency', ['one_time', 'monthly', 'quarterly', 'half_yearly', 'annually'])
                          ->default('one_time')->after('installments_count');
                }
                if (!Schema::hasColumn('fee_installment_plans', 'start_date')) {
                    $table->date('start_date')->nullable()->after('frequency');
                }
                if (!Schema::hasColumn('fee_installment_plans', 'late_fee_rule_id')) {
                    $table->unsignedBigInteger('late_fee_rule_id')->nullable()->after('start_date');
                }
                if (!Schema::hasColumn('fee_installment_plans', 'description')) {
                    $table->text('description')->nullable()->after('late_fee_rule_id');
                }
            });
        }

        if (Schema::hasTable('fee_installments')) {
            Schema::table('fee_installments', function (Blueprint $table) {
                if (!Schema::hasColumn('fee_installments', 'name')) {
                    $table->string('name')->nullable()->after('installment_number');
                }
                if (!Schema::hasColumn('fee_installments', 'amount')) {
                    $table->decimal('amount', 10, 2)->default(0)->after('name');
                }
                if (!Schema::hasColumn('fee_installments', 'fee_head_id')) {
                    $table->unsignedBigInteger('fee_head_id')->nullable()->after('amount');
                }
            });
        }
    }

    public function down(): void
    {
        // intentionally not reversing addColumn to avoid data loss
    }
};
