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
        Schema::create('fee_payment_splits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_payment_id')->constrained('fee_payments')->cascadeOnDelete();
            $table->string('payment_mode');
            $table->decimal('amount', 12, 2);
            $table->string('transaction_id')->nullable();
            $table->string('cheque_number')->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::table('fee_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_payments', 'term_number')) {
                $table->integer('term_number')->nullable()->after('fee_head_id');
            }
            if (!Schema::hasColumn('fee_payments', 'term_name')) {
                $table->string('term_name')->nullable()->after('term_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_payment_splits');

        Schema::table('fee_payments', function (Blueprint $table) {
            if (Schema::hasColumn('fee_payments', 'term_name')) {
                $table->dropColumn('term_name');
            }
            if (Schema::hasColumn('fee_payments', 'term_number')) {
                $table->dropColumn('term_number');
            }
        });
    }
};
