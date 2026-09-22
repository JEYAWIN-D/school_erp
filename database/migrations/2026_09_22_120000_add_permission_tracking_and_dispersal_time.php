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
        Schema::table('staff_attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('staff_attendance', 'in_time_auto_filled')) {
                $table->boolean('in_time_auto_filled')->default(false)->after('permission_reason');
            }
        });

        Schema::table('school_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('school_settings', 'school_dispersal_time')) {
                $table->string('school_dispersal_time', 16)->nullable()->default('16:30')->after('timezone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_attendance', function (Blueprint $table) {
            if (Schema::hasColumn('staff_attendance', 'in_time_auto_filled')) {
                $table->dropColumn('in_time_auto_filled');
            }
        });

        Schema::table('school_settings', function (Blueprint $table) {
            if (Schema::hasColumn('school_settings', 'school_dispersal_time')) {
                $table->dropColumn('school_dispersal_time');
            }
        });
    }
};
