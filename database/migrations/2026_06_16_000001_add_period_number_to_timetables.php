<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            if (!Schema::hasColumn('timetables', 'period_number')) {
                $table->tinyInteger('period_number')->nullable()->after('day_of_week');
            }
        });
    }

    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            if (Schema::hasColumn('timetables', 'period_number')) {
                $table->dropColumn('period_number');
            }
        });
    }
};
