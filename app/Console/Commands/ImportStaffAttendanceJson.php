<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StaffAttendance;
use App\Models\StaffPermissionSession;
use App\Models\Employee;
use App\Models\SchoolSetting;
use App\Http\Controllers\Admin\DashboardController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ImportStaffAttendanceJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hr:import-attendance-json 
                            {file=storage/app/attendance-2026-09.json : Path to JSON file} 
                            {--replace : Replace existing attendance records for matching employee and date} 
                            {--dry-run : Simulate import without writing to database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import staff attendance test dataset from JSON into database for development and testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        $replace = (bool) $this->option('replace');
        $dryRun = (bool) $this->option('dry-run');

        // Resolve file path
        if (!file_exists($filePath)) {
            $candidatePath = base_path($filePath);
            if (file_exists($candidatePath)) {
                $filePath = $candidatePath;
            } else {
                $storageCandidate = storage_path('app/' . basename($filePath));
                if (file_exists($storageCandidate)) {
                    $filePath = $storageCandidate;
                } else {
                    $this->error("File not found: {$filePath}");
                    return 1;
                }
            }
        }

        $this->info("Reading JSON file: {$filePath}");
        $content = file_get_contents($filePath);
        $data = json_decode($content, true);

        if (!$data || !isset($data['records']) || !is_array($data['records'])) {
            $this->error("Invalid JSON format. Expected root object with 'records' array.");
            return 1;
        }

        $records = $data['records'];
        $totalInJson = count($records);
        $month = $data['month'] ?? 'unknown';

        $tz = SchoolSetting::first()?->timezone ?: config('app.timezone', 'Asia/Kolkata');
        $today = Carbon::now($tz)->toDateString();

        // 1. Strict validation: Verify no future dates
        $futureDates = [];
        $uniqueDates = [];
        $employeeIdsInJson = [];

        foreach ($records as $r) {
            $d = $r['date'] ?? null;
            if (!$d) {
                $this->error("Encountered record with missing date.");
                return 1;
            }
            if ($d > $today) {
                $futureDates[$d] = ($futureDates[$d] ?? 0) + 1;
            }
            $uniqueDates[$d] = true;
            if (isset($r['employee_id'])) {
                $employeeIdsInJson[$r['employee_id']] = true;
            }
        }

        if (!empty($futureDates)) {
            $this->error("REJECTED: JSON contains future attendance records after today ({$today}):");
            foreach ($futureDates as $fd => $cnt) {
                $this->line("  {$fd}: {$cnt} records");
            }
            return 1;
        }

        $uniqueDatesList = array_keys($uniqueDates);
        sort($uniqueDatesList);
        $minDate = reset($uniqueDatesList);
        $maxDate = end($uniqueDatesList);
        $empIdList = array_keys($employeeIdsInJson);

        $this->info("=== ATTENDANCE JSON IMPORT INSPECTION ===");
        $this->line("Target Month:       {$month}");
        $this->line("School Timezone:    {$tz}");
        $this->line("Today's Date:       {$today}");
        $this->line("JSON Date Span:     {$minDate} to {$maxDate}");
        $this->line("Working Days:       " . count($uniqueDatesList));
        $this->line("Employees in JSON:  " . count($empIdList));
        $this->line("Total JSON Records: {$totalInJson}");
        $this->line("Mode:               " . ($dryRun ? 'DRY-RUN (No DB modifications)' : ($replace ? 'REPLACE (Update/replace matching dates)' : 'SAFE-INSERT (Skip existing records)')));

        // Inspect existing database records for these dates and employees
        $existingRecords = StaffAttendance::whereIn('date', $uniqueDatesList)
            ->whereIn('employee_id', $empIdList)
            ->get(['id', 'employee_id', 'date'])
            ->keyBy(function ($item) {
                return $item->employee_id . '_' . Carbon::parse($item->date)->toDateString();
            });

        $existingCount = $existingRecords->count();
        $this->line("Existing Records in DB for these dates: {$existingCount}");

        $wouldInsert = 0;
        $wouldReplace = 0;
        $wouldSkip = 0;

        foreach ($records as $rec) {
            $key = $rec['employee_id'] . '_' . $rec['date'];
            if (isset($existingRecords[$key])) {
                if ($replace) {
                    $wouldReplace++;
                } else {
                    $wouldSkip++;
                }
            } else {
                $wouldInsert++;
            }
        }

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Records in JSON', $totalInJson],
                ['Existing DB Records', $existingCount],
                ['Records to Insert (New)', $wouldInsert],
                ['Records to Update/Replace', $wouldReplace],
                ['Records to Skip (Preserved)', $wouldSkip],
            ]
        );

        if ($dryRun) {
            $this->info("DRY-RUN COMPLETE. No changes were made to the database.");
            return 0;
        }

        $this->info("Processing batch import...");
        $startTime = microtime(true);

        DB::transaction(function () use (
            $records,
            $existingRecords,
            $replace,
            $uniqueDatesList,
            $empIdList
        ) {
            $now = Carbon::now();

            if ($replace && $existingRecords->isNotEmpty()) {
                $existingIds = $existingRecords->pluck('id')->all();
                // Clean up old child permission sessions in batch
                foreach (array_chunk($existingIds, 500) as $chunkIds) {
                    StaffPermissionSession::whereIn('staff_attendance_id', $chunkIds)->delete();
                }
                // Delete old attendance rows in batch
                foreach (array_chunk($existingIds, 500) as $chunkIds) {
                    StaffAttendance::whereIn('id', $chunkIds)->delete();
                }
            }

            // Filter records to insert
            $toInsert = [];
            $permissionMap = []; // key: empId_date => sessions

            foreach ($records as $rec) {
                $empId = (int) $rec['employee_id'];
                $dateStr = $rec['date'];
                $key = $empId . '_' . $dateStr;

                if (!$replace && isset($existingRecords[$key])) {
                    continue; // Skip existing if replace is false
                }

                $status = $rec['status'] ?? 'present';
                $isPermission = ($status === 'permission') || !empty($rec['is_permission']);

                $toInsert[] = [
                    'employee_id'         => $empId,
                    'date'                => $dateStr,
                    'status'              => $status,
                    'check_in'            => $rec['check_in'] ?? null,
                    'check_out'           => $rec['check_out'] ?? null,
                    'remarks'             => $rec['remarks'] ?? null,
                    'is_late'             => (bool) ($rec['is_late'] ?? false),
                    'late_minutes'        => (int) ($rec['late_minutes'] ?? 0),
                    'is_permission'       => $isPermission,
                    'permission_hours'    => isset($rec['permission_hours']) ? (float) $rec['permission_hours'] : null,
                    'permission_time'     => $rec['permission_time'] ?? null,
                    'permission_reason'   => $rec['permission_reason'] ?? null,
                    'in_time_auto_filled' => (bool) ($rec['in_time_auto_filled'] ?? false),
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ];

                if ($isPermission && !empty($rec['permission_sessions']) && is_array($rec['permission_sessions'])) {
                    $permissionMap[$key] = $rec['permission_sessions'];
                }
            }

            // Bulk insert attendance rows in chunks of 500
            foreach (array_chunk($toInsert, 500) as $chunk) {
                StaffAttendance::insert($chunk);
            }

            // If there are permission sessions, look up the newly created attendance IDs and insert sessions
            if (!empty($permissionMap)) {
                $createdAttendances = StaffAttendance::whereIn('date', $uniqueDatesList)
                    ->whereIn('employee_id', $empIdList)
                    ->where(function ($q) {
                        $q->where('status', 'permission')->orWhere('is_permission', true);
                    })
                    ->get(['id', 'employee_id', 'date']);

                $sessionsToInsert = [];
                foreach ($createdAttendances as $att) {
                    $dateStr = Carbon::parse($att->date)->toDateString();
                    $k = $att->employee_id . '_' . $dateStr;
                    if (isset($permissionMap[$k])) {
                        foreach ($permissionMap[$k] as $idx => $sess) {
                            $sessionsToInsert[] = [
                                'staff_attendance_id' => $att->id,
                                'session_order'       => $sess['session_order'] ?? ($idx + 1),
                                'out_time'            => $sess['out_time'] ?? '14:00:00',
                                'in_time'             => $sess['in_time'] ?? null,
                                'in_time_auto_filled' => (bool) ($sess['in_time_auto_filled'] ?? false),
                                'created_at'          => $now,
                                'updated_at'          => $now,
                            ];
                        }
                    }
                }

                if (!empty($sessionsToInsert)) {
                    foreach (array_chunk($sessionsToInsert, 500) as $sChunk) {
                        StaffPermissionSession::insert($sChunk);
                    }
                }
            }
        });

        $duration = round(microtime(true) - $startTime, 2);
        DashboardController::clearCache();

        $this->info("SUCCESS: Staff attendance dataset imported successfully in {$duration} seconds!");
        $this->table(
            ['Action', 'Count'],
            [
                ['Inserted (New Records)', $replace ? $totalInJson : $wouldInsert],
                ['Replaced / Updated', $replace ? $existingCount : 0],
                ['Skipped (Preserved)', $replace ? 0 : $wouldSkip],
                ['Total Processed', $totalInJson],
                ['Execution Time', "{$duration}s"],
            ]
        );

        // Verification & Integrity Check
        $activeEmployees = Employee::where('status', 'active')->pluck('id');
        $activeCount = $activeEmployees->count();

        $dbTotal = StaffAttendance::whereIn('date', $uniqueDatesList)
            ->whereIn('employee_id', $activeEmployees)
            ->count();

        $this->info("=== DATABASE VERIFICATION FOR SEPTEMBER 2026 ===");
        $this->line("Active Employees:                   {$activeCount}");
        $this->line("Working Days in Period:             " . count($uniqueDatesList));
        $this->line("Total Attendance Records in DB:     {$dbTotal}");
        $this->line("Expected Records (129 × 23):        " . ($activeCount * count($uniqueDatesList)));

        // Test check specific employees
        $testEmp1 = StaffAttendance::where('employee_id', 1)->whereBetween('date', [$minDate, $maxDate])->get();
        $p1 = $testEmp1->where('status', 'present')->count();
        $totalDays1 = $testEmp1->count();
        $rate1 = count($uniqueDatesList) > 0 ? number_format(round(($p1 / count($uniqueDatesList)) * 100, 2), 2) : '0.00';
        $this->line("Employee 1 (Rajesh Sharma): Present {$p1}/{$totalDays1} -> Rate: {$rate1}% (Expected: 100.00%)");

        $testEmp2 = StaffAttendance::where('employee_id', 2)->whereBetween('date', [$minDate, $maxDate])->get();
        $p2 = $testEmp2->where('status', 'present')->count();
        $a2 = $testEmp2->where('status', 'absent')->count();
        $rate2 = count($uniqueDatesList) > 0 ? number_format(round(($p2 / count($uniqueDatesList)) * 100, 2), 2) : '0.00';
        $this->line("Employee 2 (Sunita Verma): Present {$p2}, Absent {$a2} -> Rate: {$rate2}% (Expected: 95.65%)");

        $testEmp3 = StaffAttendance::where('employee_id', 3)->whereBetween('date', [$minDate, $maxDate])->get();
        $p3 = $testEmp3->where('status', 'present')->count();
        $a3 = $testEmp3->where('status', 'absent')->count();
        $h3 = $testEmp3->where('status', 'half_day')->count();
        $rate3 = count($uniqueDatesList) > 0 ? number_format(round(($p3 / count($uniqueDatesList)) * 100, 2), 2) : '0.00';
        $this->line("Employee 3 (Anil Kumar): Present {$p3}, Absent {$a3}, Half Day {$h3} -> Present Rate: {$rate3}% (Expected: 86.96%)");

        return 0;
    }
}
