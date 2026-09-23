<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dashboard_widgets', function (Blueprint $table) {
            // Source key — identifies which data source this card reads from.
            // e.g. 'student_count', 'staff_total', 'fee_collected_today'
            $table->string('source', 60)->default('student_count')->after('module');

            // is_default — marks the 6 original KPI cards seeded as defaults.
            // Default cards ARE deletable but show a warning.
            $table->boolean('is_default')->default(false)->after('is_active');
        });

        // Migrate existing rows: if module='student', source='student_count'
        \DB::table('dashboard_widgets')
            ->where('module', 'student')
            ->update(['source' => 'student_count']);
    }

    public function down(): void
    {
        Schema::table('dashboard_widgets', function (Blueprint $table) {
            $table->dropColumn(['source', 'is_default']);
        });
    }
};

