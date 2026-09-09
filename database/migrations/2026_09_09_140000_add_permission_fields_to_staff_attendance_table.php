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
            if (!Schema::hasColumn('staff_attendance', 'is_permission')) {
                $table->boolean('is_permission')->default(false)->after('late_minutes');
            }
            if (!Schema::hasColumn('staff_attendance', 'permission_hours')) {
                $table->decimal('permission_hours', 3, 1)->nullable()->after('is_permission');
            }
            if (!Schema::hasColumn('staff_attendance', 'permission_time')) {
                $table->string('permission_time', 64)->nullable()->after('permission_hours');
            }
            if (!Schema::hasColumn('staff_attendance', 'permission_reason')) {
                $table->string('permission_reason', 255)->nullable()->after('permission_time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_attendance', function (Blueprint $table) {
            $table->dropColumn(['is_permission', 'permission_hours', 'permission_time', 'permission_reason']);
        });
    }
};
