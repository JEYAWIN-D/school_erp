<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->string('payment_terms')->nullable()->after('previous_percentage');
            $table->decimal('total_admission_fee', 10, 2)->default(0)->after('payment_terms');
            $table->decimal('amount_collected', 10, 2)->default(0)->after('total_admission_fee');
            $table->decimal('pending_amount', 10, 2)->default(0)->after('amount_collected');
            $table->string('payment_mode')->nullable()->after('pending_amount');
            $table->date('payment_date')->nullable()->after('payment_mode');
            $table->string('payment_status')->default('pending')->after('payment_date');
            $table->json('fee_breakdown')->nullable()->after('payment_status');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('payment_terms')->nullable()->after('status');
            $table->decimal('total_admission_fee', 10, 2)->default(0)->after('payment_terms');
            $table->decimal('admission_paid_amount', 10, 2)->default(0)->after('total_admission_fee');
            $table->decimal('admission_pending_amount', 10, 2)->default(0)->after('admission_paid_amount');
            $table->string('payment_mode')->nullable()->after('admission_pending_amount');
            $table->date('payment_date')->nullable()->after('payment_mode');
            $table->string('payment_status')->default('pending')->after('payment_date');
            $table->json('admission_fee_terms')->nullable()->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn([
                'payment_terms', 'total_admission_fee', 'amount_collected',
                'pending_amount', 'payment_mode', 'payment_date', 'payment_status', 'fee_breakdown'
            ]);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'payment_terms', 'total_admission_fee', 'admission_paid_amount',
                'admission_pending_amount', 'payment_mode', 'payment_date', 'payment_status', 'admission_fee_terms'
            ]);
        });
    }
};
