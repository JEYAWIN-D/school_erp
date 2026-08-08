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
        Schema::table('payroll_records', function (Blueprint $table) {
            if (!Schema::hasColumn('payroll_records', 'tds_amount')) {
                $table->decimal('tds_amount', 10, 2)->default(0)->after('net_salary');
            }
        });
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'investment_80c')) {
                $table->decimal('investment_80c', 10, 2)->default(0)->after('esi_no');
            }
            if (!Schema::hasColumn('employees', 'investment_80d')) {
                $table->decimal('investment_80d', 10, 2)->default(0)->after('investment_80c');
            }
            if (!Schema::hasColumn('employees', 'hra_exemption')) {
                $table->decimal('hra_exemption', 10, 2)->default(0)->after('investment_80d');
            }
            if (!Schema::hasColumn('employees', 'tax_regime')) {
                $table->string('tax_regime', 10)->default('new')->after('hra_exemption');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payroll_records', function (Blueprint $table) {
            $table->dropColumn('tds_amount');
        });
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['investment_80c', 'investment_80d', 'hra_exemption', 'tax_regime']);
        });
    }
};
