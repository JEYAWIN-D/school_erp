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
        Schema::table('timetables', function (Blueprint $table) {
            if (!Schema::hasColumn('timetables', 'period_type')) {
                $table->enum('period_type', ['class', 'break', 'lunch', 'free'])->default('class')->after('end_time');
            }
            if (!Schema::hasColumn('timetables', 'effective_from')) {
                $table->date('effective_from')->nullable()->after('period_type');
            }
            if (!Schema::hasColumn('timetables', 'day_of_week')) {
                $table->unsignedTinyInteger('day_of_week')->nullable()->after('day')
                    ->comment('1=Mon, 2=Tue, ... 6=Sat, 0=Sun');
            }
        });
    }

    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            $table->dropColumn(['period_type', 'effective_from', 'day_of_week']);
        });
    }
};
