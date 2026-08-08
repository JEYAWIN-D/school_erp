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
        Schema::table('fee_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_payments', 'cheque_number')) {
                $table->string('cheque_number', 20)->nullable()->after('transaction_id');
            }
            if (!Schema::hasColumn('fee_payments', 'cheque_bank')) {
                $table->string('cheque_bank', 100)->nullable()->after('cheque_number');
            }
            if (!Schema::hasColumn('fee_payments', 'cheque_branch')) {
                $table->string('cheque_branch', 100)->nullable()->after('cheque_bank');
            }
            if (!Schema::hasColumn('fee_payments', 'cheque_date')) {
                $table->date('cheque_date')->nullable()->after('cheque_branch');
            }
            if (!Schema::hasColumn('fee_payments', 'cheque_status')) {
                $table->enum('cheque_status', ['pending', 'cleared', 'bounced'])->nullable()->after('cheque_date');
            }
            if (!Schema::hasColumn('fee_payments', 'bounce_charge')) {
                $table->decimal('bounce_charge', 8, 2)->default(0)->after('cheque_status');
            }
            if (!Schema::hasColumn('fee_payments', 'bounce_reason')) {
                $table->string('bounce_reason')->nullable()->after('bounce_charge');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            $table->dropColumn(['cheque_number', 'cheque_bank', 'cheque_branch', 'cheque_date', 'cheque_status', 'bounce_charge', 'bounce_reason']);
        });
    }
};
