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
        Schema::table('attendance_records', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_records', 'arrival_time')) {
                $table->time('arrival_time')->nullable()->after('status');
            }
            if (!Schema::hasColumn('attendance_records', 'departure_time')) {
                $table->time('departure_time')->nullable()->after('arrival_time');
            }
            if (!Schema::hasColumn('attendance_records', 'is_late')) {
                $table->boolean('is_late')->default(false)->after('departure_time');
            }
            if (!Schema::hasColumn('attendance_records', 'cutoff_override')) {
                $table->boolean('cutoff_override')->default(false)->after('is_late');
            }
            if (!Schema::hasColumn('attendance_records', 'cutoff_override_by')) {
                $table->foreignId('cutoff_override_by')->nullable()->constrained('users')->nullOnDelete()->after('cutoff_override');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropColumn(['arrival_time', 'departure_time', 'is_late', 'cutoff_override', 'cutoff_override_by']);
        });
    }
};
