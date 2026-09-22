<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add payment_account to fee_payments
        if (Schema::hasTable('fee_payments') && !Schema::hasColumn('fee_payments', 'payment_account')) {
            Schema::table('fee_payments', function (Blueprint $table) {
                $table->string('payment_account', 50)->default('cash_box_1')->nullable()->index();
            });
        }

        // 2. Add payment_account to fee_payment_splits
        if (Schema::hasTable('fee_payment_splits') && !Schema::hasColumn('fee_payment_splits', 'payment_account')) {
            Schema::table('fee_payment_splits', function (Blueprint $table) {
                $table->string('payment_account', 50)->default('cash_box_1')->nullable();
            });
        }

        // 3. Add payment_account to expenses
        if (Schema::hasTable('expenses') && !Schema::hasColumn('expenses', 'payment_account')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->string('payment_account', 50)->default('cash_box_1')->nullable()->index();
            });
        }

        // 4. Add payment_account to students
        if (Schema::hasTable('students') && !Schema::hasColumn('students', 'payment_account')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('payment_account', 50)->nullable();
            });
        }

        // 5. Add payment_account to enquiries
        if (Schema::hasTable('enquiries') && !Schema::hasColumn('enquiries', 'payment_account')) {
            Schema::table('enquiries', function (Blueprint $table) {
                $table->string('payment_account', 50)->nullable();
            });
        }

        // 6. Create account_transfers table for inter-account fund movements
        if (!Schema::hasTable('account_transfers')) {
            Schema::create('account_transfers', function (Blueprint $table) {
                $table->id();
                $table->string('transfer_number', 64)->unique();
                $table->string('from_account', 50)->index(); // upi, cash_box_1, cash_box_2, bank
                $table->string('to_account', 50)->index();   // upi, cash_box_1, cash_box_2, bank
                $table->decimal('amount', 12, 2);
                $table->date('transfer_date')->index();
                $table->string('reference_no')->nullable();
                $table->text('remarks')->nullable();
                $table->foreignId('transferred_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 7. Data backfill: Sync existing rows to payment_account
        try {
            DB::statement("UPDATE fee_payments SET payment_account = 'upi' WHERE LOWER(payment_mode) = 'upi' AND (payment_account IS NULL OR payment_account = 'cash_box_1')");
            DB::statement("UPDATE fee_payments SET payment_account = 'cash_box_1' WHERE LOWER(payment_mode) IN ('cash', 'net banking', 'online') AND payment_account IS NULL");

            DB::statement("UPDATE expenses SET payment_account = 'upi' WHERE LOWER(payment_method) = 'upi' AND (payment_account IS NULL OR payment_account = 'cash_box_1')");
            DB::statement("UPDATE expenses SET payment_account = 'cash_box_1' WHERE LOWER(payment_method) LIKE '%cash%' AND (payment_account IS NULL OR payment_account = 'cash_box_1')");
            DB::statement("UPDATE expenses SET payment_account = 'cash_box_2' WHERE LOWER(payment_method) LIKE '%cheque%' AND (payment_account IS NULL OR payment_account = 'cash_box_1')");
        } catch (\Throwable $e) {
            // Ignore if tables are empty
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_transfers');

        if (Schema::hasColumn('enquiries', 'payment_account')) {
            Schema::table('enquiries', function (Blueprint $table) {
                $table->dropColumn('payment_account');
            });
        }

        if (Schema::hasColumn('students', 'payment_account')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('payment_account');
            });
        }

        if (Schema::hasColumn('expenses', 'payment_account')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->dropColumn('payment_account');
            });
        }

        if (Schema::hasColumn('fee_payment_splits', 'payment_account')) {
            Schema::table('fee_payment_splits', function (Blueprint $table) {
                $table->dropColumn('payment_account');
            });
        }

        if (Schema::hasColumn('fee_payments', 'payment_account')) {
            Schema::table('fee_payments', function (Blueprint $table) {
                $table->dropColumn('payment_account');
            });
        }
    }
};
