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

            $query = StaffAttendance::where('status', 'permission')
                ->whereNotNull('check_out')
                ->whereNull('check_in');

            if ($specificDate) {
                // If specific date requested
                if ($specificDate < $today) {
                    $query->whereDate('date', $specificDate);
                } elseif ($specificDate === $today && $nowHi >= $dispersalHi) {
                    $query->whereDate('date', $specificDate);
                } else {
                    return 0; // Dispersal not yet reached for today, or future date
                }
            } else {
                // Process past dates OR today if now >= dispersal time
                $query->where(function ($q) use ($today, $nowHi, $dispersalHi) {
                    $q->whereDate('date', '<', $today);
                    if ($nowHi >= $dispersalHi) {
                        $q->orWhereDate('date', $today);
                    }
                });
            }

            $records = $query->get();
            $updated = 0;

            foreach ($records as $record) {
                $record->update([
                    'check_in'            => $dispersalTime,
                    'in_time_auto_filled' => true,
                    'is_permission'       => true,
                ]);
                $updated++;
            }

            return $updated;
        } catch (\Exception $e) {
            Log::error("Failed to execute Permission In-Time auto-fill: " . $e->getMessage());
            return 0;
        }
    }
}
