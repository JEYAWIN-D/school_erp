<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\SchoolSetting;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class GenerateStaffAttendanceJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hr:generate-attendance-json 
                            {month=2026-09 : Target month in YYYY-MM format} 
                            {--output= : Destination path for JSON file (default: storage/app/attendance-{month}.json)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate complete, realistic, deterministic staff attendance test dataset for elapsed working days of a month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $month = $this->argument('month') ?: '2026-09';

        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $this->error("Invalid month format: {$month}. Expected YYYY-MM.");
            return 1;
        }

        $tz = SchoolSetting::first()?->timezone ?: config('app.timezone', 'Asia/Kolkata');
        $today = Carbon::now($tz)->toDateString();

        $startDate = Carbon::parse($month . '-01')->toDateString();
        $monthEnd  = Carbon::parse($startDate)->endOfMonth()->toDateString();
        $endDate   = min($today, $monthEnd);

        // Build list of elapsed working days (excluding Sundays only)
        $period = CarbonPeriod::create($startDate, $endDate);
        $workingDates = [];
        foreach ($period as $d) {
            if (!$d->isSunday() && $d->toDateString() <= $today) {
                $workingDates[] = $d->toDateString();
            }
        }

        $workingDaysCount = count($workingDates);

        if ($workingDaysCount === 0) {
            $this->error("No valid working days found for {$month} up to {$today}.");
            return 1;
        }

        // Active employees
        $employees = Employee::where('status', 'active')->orderBy('id')->get();
        $employeeCount = $employees->count();

        if ($employeeCount === 0) {
            $this->error('No active employees found in the database.');
            return 1;
        }

        $expectedTotalRecords = $employeeCount * $workingDaysCount;

        $this->info("=== GENERATING ATTENDANCE TEST DATA ===");
        $this->line("Target Month:       {$month}");
        $this->line("School Timezone:    {$tz}");
        $this->line("Today's Date:       {$today}");
        $this->line("Date Range:         {$startDate} to {$endDate}");
        $this->line("Total Working Days: {$workingDaysCount} (Sundays excluded, strictly <= {$today})");
        $this->line("Active Employees:   {$employeeCount}");
        $this->line("Expected Records:   {$expectedTotalRecords} ({$employeeCount} × {$workingDaysCount})");

        $records = [];

        foreach ($employees as $empIndex => $emp) {
            foreach ($workingDates as $dayIndex => $dateStr) {
                $rec = $this->buildRecordForEmployee($emp, $empIndex, $dateStr, $dayIndex, $workingDaysCount);
                $records[] = $rec;
            }
        }

        $payload = [
            'month'           => $month,
            'generated_at'    => Carbon::now($tz)->toIso8601String(),
            'working_days'    => $workingDaysCount,
            'working_dates'   => $workingDates,
            'total_employees' => $employeeCount,
            'total_records'   => count($records),
            'records'         => $records,
        ];

        $outputPath = $this->option('output');
        if (empty($outputPath)) {
            $outputPath = storage_path("app/attendance-{$month}.json");
        } elseif (!str_starts_with($outputPath, '/') && !str_starts_with($outputPath, '\\') && !preg_match('/^[a-zA-Z]:/', $outputPath)) {
            $outputPath = base_path($outputPath);
        }

        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($outputPath, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->info("SUCCESS: Generated {$payload['total_records']} attendance records.");
        $this->line("Output File: {$outputPath}");
        $this->line("File Size:   " . number_format(filesize($outputPath) / 1024, 2) . " KB");

        $this->table(
            ['Key', 'Value'],
            [
                ['Month', $month],
                ['Working Days', $workingDaysCount],
                ['Active Employees', $employeeCount],
                ['Total Records', count($records)],
                ['Saved Path', $outputPath],
            ]
        );

        return 0;
    }

    /**
     * Deterministically build an attendance record for an employee on a given working day.
     */
    protected function buildRecordForEmployee(Employee $emp, int $empIndex, string $dateStr, int $dayIndex, int $totalWorkingDays): array
    {
        $base = [
            'employee_id'         => $emp->id,
            'employee_code'       => $emp->employee_code,
            'employee_name'       => $emp->full_name,
            'date'                => $dateStr,
            'is_late'             => false,
            'late_minutes'        => 0,
            'is_permission'       => false,
            'permission_hours'    => null,
            'permission_time'     => null,
            'permission_reason'   => null,
            'in_time_auto_filled' => false,
            'permission_sessions' => [],
        ];

        // Specific test patterns for deterministic verification:
        // Employee 1 (empIndex 0): 23 Present -> 100.00%
        if ($empIndex === 0) {
            return array_merge($base, [
                'status'    => 'present',
                'check_in'  => '08:30:00',
                'check_out' => '16:30:00',
                'remarks'   => 'Regular Present',
            ]);
        }

        // Employee 2 (empIndex 1): 22 Present, 1 Absent -> 95.65%
        if ($empIndex === 1) {
            if ($dayIndex === 4) { // 2026-09-05
                return array_merge($base, [
                    'status'    => 'absent',
                    'check_in'  => null,
                    'check_out' => null,
                    'remarks'   => 'Uninformed Absent',
                ]);
            }
            return array_merge($base, [
                'status'    => 'present',
                'check_in'  => '08:30:00',
                'check_out' => '16:30:00',
                'remarks'   => 'Regular Present',
            ]);
        }

        // Employee 3 (empIndex 2): 20 Present, 2 Absent, 1 Half Day -> Present-based: 86.96%
        if ($empIndex === 2) {
            if ($dayIndex === 6) { // 2026-09-08
                return array_merge($base, [
                    'status'    => 'absent',
                    'check_in'  => null,
                    'check_out' => null,
                    'remarks'   => 'Medical Absent',
                ]);
            }
            if ($dayIndex === 13) { // 2026-09-16
                return array_merge($base, [
                    'status'    => 'absent',
                    'check_in'  => null,
                    'check_out' => null,
                    'remarks'   => 'Personal Absent',
                ]);
            }
            if ($dayIndex === 20) { // 2026-09-24
                return array_merge($base, [
                    'status'    => 'half_day',
                    'check_in'  => '08:30:00',
                    'check_out' => '12:30:00',
                    'remarks'   => 'Half Day - Afternoon Leave',
                ]);
            }
            return array_merge($base, [
                'status'    => 'present',
                'check_in'  => '08:30:00',
                'check_out' => '16:30:00',
                'remarks'   => 'Regular Present',
            ]);
        }

        // Employee 4 (empIndex 3): 17 Present, 6 Absent -> 73.91%
        if ($empIndex === 3) {
            if (in_array($dayIndex, [2, 7, 11, 15, 18, 22])) {
                return array_merge($base, [
                    'status'    => 'absent',
                    'check_in'  => null,
                    'check_out' => null,
                    'remarks'   => 'Absent',
                ]);
            }
            return array_merge($base, [
                'status'    => 'present',
                'check_in'  => '08:30:00',
                'check_out' => '16:30:00',
                'remarks'   => 'Regular Present',
            ]);
        }

        // Employees index 4 to 12 (empIndex 4..12): All 23 Present -> 100.00%
        // Provides multiple employees with 100% attendance as required
        if ($empIndex >= 4 && $empIndex <= 12) {
            return array_merge($base, [
                'status'    => 'present',
                'check_in'  => '08:30:00',
                'check_out' => '16:30:00',
                'remarks'   => 'Regular Present',
            ]);
        }

        // Remaining active employees (empIndex 13 to 128): Deterministic distribution
        $hash = (($emp->id * 31) + ($dayIndex * 17)) % 100;

        if ($hash < 82) { // 82% Present
            return array_merge($base, [
                'status'    => 'present',
                'check_in'  => '08:30:00',
                'check_out' => '16:30:00',
                'remarks'   => 'Regular Present',
            ]);
        }

        if ($hash < 88) { // 6% Absent
            return array_merge($base, [
                'status'    => 'absent',
                'check_in'  => null,
                'check_out' => null,
                'remarks'   => 'Absent',
            ]);
        }

        if ($hash < 92) { // 4% Half Day
            return array_merge($base, [
                'status'    => 'half_day',
                'check_in'  => '08:30:00',
                'check_out' => '12:30:00',
                'remarks'   => 'Half Day',
            ]);
        }

        if ($hash < 95) { // 3% On Duty
            return array_merge($base, [
                'status'    => 'on_duty',
                'check_in'  => '08:30:00',
                'check_out' => '16:30:00',
                'remarks'   => 'On Duty - Academic Program',
            ]);
        }

        if ($hash < 98) { // 3% Paid Off
            return array_merge($base, [
                'status'    => 'paid_off',
                'check_in'  => '08:30:00',
                'check_out' => '16:30:00',
                'remarks'   => 'Paid Off - School Event',
            ]);
        }

        // Remaining ~2%: Permission with permission sessions
        return array_merge($base, [
            'status'              => 'permission',
            'check_in'            => '08:30:00',
            'check_out'           => '16:30:00',
            'remarks'             => 'Permission - Official/Personal',
            'is_permission'       => true,
            'permission_hours'    => 1.5,
            'permission_time'     => '02:00 PM',
            'permission_reason'   => 'Personal Permission',
            'in_time_auto_filled' => false,
            'permission_sessions' => [
                [
                    'session_order'       => 1,
                    'out_time'            => '14:00:00',
                    'in_time'             => '15:30:00',
                    'in_time_auto_filled' => false,
                ]
            ],
        ]);
    }
}
