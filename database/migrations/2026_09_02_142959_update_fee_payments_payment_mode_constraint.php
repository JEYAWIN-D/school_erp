<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            DB::statement('ALTER TABLE fee_payments DROP CONSTRAINT IF EXISTS fee_payments_payment_mode_check');
        } catch (\Throwable $e) {
            // Ignore if constraint does not exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
