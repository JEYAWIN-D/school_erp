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
        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'term_label')) {
                $table->string('term_label', 30)->nullable(); // e.g. "Term 1", "Half-Yearly", "Annual"
            }
            if (!Schema::hasColumn('exams', 'weightage_percent')) {
                $table->decimal('weightage_percent', 5, 2)->nullable(); // e.g. 30.00 = 30% weight
            }
            if (!Schema::hasColumn('exams', 'is_cumulative_component')) {
                $table->boolean('is_cumulative_component')->default(false); // include in cumulative
            }
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $cols = array_filter(['term_label','weightage_percent','is_cumulative_component'], fn($c) => Schema::hasColumn('exams', $c));
            if ($cols) $table->dropColumn($cols);
        });
    }
};
