<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StaffAttendance;
use App\Models\SchoolSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoFillPermissionInTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hr:autofill-permission-in-time {--date= : Specific date to process (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-fill blank In-Time for staff marked with Permission once school dispersal time is reached';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = self::executeAutoFill($this->option('date'));
        $this->info("Auto-fill complete. Updated {$count} permission attendance record(s).");
        return self::SUCCESS;
    }

    /**
     * Static helper to run auto-fill from anywhere (Command, Controller, Scheduler).
     * Returns count of updated records.
     */
    public static function executeAutoFill(?string $specificDate = null): int
    {
        try {
            $dispersalTimeStr = SchoolSetting::get('school_dispersal_time', SchoolSetting::get('staff_shift_end_time', '16:30'));
            if (empty($dispersalTimeStr)) {
                $dispersalTimeStr = '16:30';
            }

            // Standardize format to H:i
            $dispersalTime = Carbon::createFromTimeString($dispersalTimeStr)->format('H:i:s');
            $dispersalHi   = Carbon::createFromTimeString($dispersalTimeStr)->format('H:i');

            $today = today()->toDateString();
            $nowHi = now()->format('H:i');

            $sessionQuery = \App\Models\StaffPermissionSession::whereNull('in_time')
                ->whereNotNull('out_time')
                ->whereHas('staffAttendance', function ($q) use ($specificDate, $today, $nowHi, $dispersalHi) {
                    $q->where(function ($sq) {
                        $sq->where('status', 'permission')
                           ->orWhere('is_permission', true);
                    });

                    if ($specificDate) {
                        $q->whereDate('date', $specificDate);
                    } else {
                        $q->where(function ($sub) use ($today, $nowHi, $dispersalHi) {
                            $sub->whereDate('date', '<', $today);
                            if ($nowHi >= $dispersalHi) {
                                $sub->orWhereDate('date', $today);
                            }
                        });
                    }
                });

            if ($specificDate) {
                if ($specificDate === $today && $nowHi < $dispersalHi) {
                    return 0; // Dispersal not yet reached for today
                } elseif ($specificDate > $today) {
                    return 0; // Future date
                }
            }

            $sessions = $sessionQuery->with('staffAttendance')->get();
            $updated = 0;

            foreach ($sessions as $session) {
                // Only auto-fill if in_time is genuinely null (never overwrite manual in-time)
                if (is_null($session->in_time)) {
                    $session->update([
                        'in_time'             => $dispersalTime,
                        'in_time_auto_filled' => true,
                    ]);

                    // Sync legacy columns on parent daily attendance record if needed
                    $parent = $session->staffAttendance;
                    if ($parent && (is_null($parent->check_in) || $parent->in_time_auto_filled)) {
                        $parent->update([
                            'check_in'            => $dispersalTime,
                            'in_time_auto_filled' => true,
                            'is_permission'       => true,
                        ]);
                    }

                    $updated++;
                }
            }

            // Also check any legacy standalone staff_attendance records without sessions
            $legacyQuery = StaffAttendance::where('status', 'permission')
                ->whereNotNull('check_out')
                ->whereNull('check_in');

            if ($specificDate) {
                if ($specificDate < $today || ($specificDate === $today && $nowHi >= $dispersalHi)) {
                    $legacyQuery->whereDate('date', $specificDate);
                } else {
                    $legacyQuery = null;
                }
            } else {
                $legacyQuery->where(function ($q) use ($today, $nowHi, $dispersalHi) {
                    $q->whereDate('date', '<', $today);
                    if ($nowHi >= $dispersalHi) {
                        $q->orWhereDate('date', $today);
                    }
                });
            }

            if ($legacyQuery) {
                foreach ($legacyQuery->get() as $rec) {
                    $rec->update([
                        'check_in'            => $dispersalTime,
                        'in_time_auto_filled' => true,
                        'is_permission'       => true,
                    ]);
                    // Create session record if none exists
                    \App\Models\StaffPermissionSession::firstOrCreate(
                        ['staff_attendance_id' => $rec->id, 'out_time' => $rec->check_out],
                        ['in_time' => $dispersalTime, 'in_time_auto_filled' => true, 'session_order' => 1]
                    );
                }
            }

            return $updated;
        } catch (\Exception $e) {
            Log::error("Failed to execute Permission In-Time auto-fill: " . $e->getMessage());
            return 0;
        }
    }
}
