<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('staff_attendance', 'is_late')) {
                $table->boolean('is_late')->default(false)->after('check_in');
            }
            if (!Schema::hasColumn('staff_attendance', 'late_minutes')) {
                $table->unsignedSmallInteger('late_minutes')->default(0)->after('is_late');
            }
        });
    }

    public function down(): void
    {
        Schema::table('staff_attendance', function (Blueprint $table) {
            $table->dropColumn(['is_late', 'late_minutes']);
        });
    }
};
