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
        Schema::table('fee_heads', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_heads', 'gst_applicable')) {
                $table->boolean('gst_applicable')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('fee_heads', 'gst_percent')) {
                $table->decimal('gst_percent', 5, 2)->default(0)->after('gst_applicable');
            }
            if (!Schema::hasColumn('fee_heads', 'hsn_code')) {
                $table->string('hsn_code', 20)->nullable()->after('gst_percent');
            }
            if (!Schema::hasColumn('fee_heads', 'gst_type')) {
                $table->enum('gst_type', ['cgst_sgst', 'igst'])->default('cgst_sgst')->after('hsn_code');
            }
            if (!Schema::hasColumn('fee_heads', 'tally_ledger_name')) {
                $table->string('tally_ledger_name')->nullable()->after('gst_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fee_heads', function (Blueprint $table) {
            $cols = array_filter(['gst_applicable','gst_percent','hsn_code','gst_type','tally_ledger_name'], fn($c) => Schema::hasColumn('fee_heads', $c));
            if ($cols) $table->dropColumn($cols);
        });
    }
};
