<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('fee_advance_payments')) {
            Schema::create('fee_advance_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('academic_year_id')->nullable();
                $table->decimal('amount', 10, 2);
                $table->decimal('applied_amount', 10, 2)->default(0);
                $table->decimal('remaining_amount', 10, 2);
                $table->date('payment_date');
                $table->string('receipt_number')->nullable();
                $table->enum('payment_mode', ['cash','cheque','dd','online','upi'])->default('cash');
                $table->string('transaction_id')->nullable();
                $table->string('notes')->nullable();
                $table->unsignedBigInteger('recorded_by')->nullable();
                $table->timestamps();
                $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            });
        }

        // Track which fee payment had advance applied
        if (Schema::hasTable('fee_payments') && !Schema::hasColumn('fee_payments', 'advance_applied')) {
            Schema::table('fee_payments', function (Blueprint $table) {
                $table->decimal('advance_applied', 10, 2)->default(0)->after('discount');
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('fee_advance_payments');
        if (Schema::hasTable('fee_payments') && Schema::hasColumn('fee_payments', 'advance_applied')) {
            Schema::table('fee_payments', function (Blueprint $table) {
                $table->dropColumn('advance_applied');
            });
        }
    }
};
