<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('staff_permission_sessions')) {
            Schema::create('staff_permission_sessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('staff_attendance_id');
                $table->unsignedSmallInteger('session_order')->default(1);
                $table->time('out_time');
                $table->time('in_time')->nullable();
                $table->boolean('in_time_auto_filled')->default(false);
                $table->timestamps();

                $table->foreign('staff_attendance_id')
                      ->references('id')
                      ->on('staff_attendance')
                      ->cascadeOnDelete();

                $table->index('staff_attendance_id');
                $table->index(['staff_attendance_id', 'session_order']);
            });
        }

        // Migrate existing permission records:
        // Rule: ONLY records positively identified as Permission ('status' = 'permission' OR 'is_permission' = true), NEVER check_out alone.
        $existingPermissions = DB::table('staff_attendance')
            ->where(function ($query) {
                $query->where('status', 'permission')
                      ->orWhere('is_permission', true);
            })
            ->whereNotNull('check_out')
            ->get();

        $now = now();
        foreach ($existingPermissions as $record) {
            // Check if already migrated
            $exists = DB::table('staff_permission_sessions')
                ->where('staff_attendance_id', $record->id)
                ->where('out_time', $record->check_out)
                ->exists();

            if (!$exists) {
                DB::table('staff_permission_sessions')->insert([
                    'staff_attendance_id' => $record->id,
                    'session_order'       => 1,
                    'out_time'            => $record->check_out,
                    'in_time'             => $record->check_in,
                    'in_time_auto_filled' => (bool) ($record->in_time_auto_filled ?? false),
                    'created_at'          => $record->created_at ?? $now,
                    'updated_at'          => $record->updated_at ?? $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_permission_sessions');
    }
};
