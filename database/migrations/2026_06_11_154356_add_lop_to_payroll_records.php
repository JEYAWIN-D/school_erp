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
            if (!Schema::hasColumn('payroll_records', 'lop_days')) {
                $table->unsignedTinyInteger('lop_days')->default(0);
            }
            if (!Schema::hasColumn('payroll_records', 'lop_amount')) {
                $table->decimal('lop_amount', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('payroll_records', 'working_days')) {
                $table->unsignedTinyInteger('working_days')->default(26);
            }
            if (!Schema::hasColumn('payroll_records', 'present_days')) {
                $table->unsignedTinyInteger('present_days')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('payroll_records', function (Blueprint $table) {
            $cols = ['lop_days','lop_amount','working_days','present_days'];
            $toDrop = array_filter($cols, fn($c) => Schema::hasColumn('payroll_records', $c));
            if ($toDrop) $table->dropColumn($toDrop);
        });
    }
};
