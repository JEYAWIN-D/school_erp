<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;
use App\Models\StaffAttendance;
use Carbon\Carbon;

$employees = Employee::where('is_active', true)->take(12)->get();
$today = Carbon::today();
$currentMonth = $today->copy()->startOfMonth();

$reasons = [
    'Doctor Visit / Medical',
    'Bank Duty / School Work',
    'Personal Emergency',
    'Early Departure Approved by Principal',
    'Govt / Education Dept Submission',
];

echo "Seeding sample staff attendance for {$employees->count()} employees across current month...\n";

foreach ($employees as $idx => $emp) {
    // Seed 5 to 7 days in the current month
    for ($day = 1; $day <= min(9, $today->day); $day++) {
        $date = Carbon::create($today->year, $today->month, $day);
        if ($date->isSunday()) continue;

        // Give a couple of employees approved permissions
        if (($idx == 0 && $day == 4) || ($idx == 1 && $day == 6) || ($idx == 2 && $day == $today->day) || ($idx == 4 && $day == 8)) {
            StaffAttendance::updateOrCreate(
                ['employee_id' => $emp->id, 'date' => $date->toDateString()],
                [
                    'status'            => 'permission',
                    'is_permission'     => true,
                    'permission_hours'  => ($day % 2 == 0) ? 1.5 : 2.0,
                    'permission_time'   => '09:00 - 10:30 AM',
                    'permission_reason' => $reasons[$idx % count($reasons)],
                    'check_in'          => '10:30:00',
                    'check_out'         => '16:30:00',
                    'remarks'           => 'Approved Permission by Principal',
                ]
            );
        } elseif ($day == 2 && $idx % 3 == 0) {
            StaffAttendance::updateOrCreate(
                ['employee_id' => $emp->id, 'date' => $date->toDateString()],
                [
                    'status'            => 'half_day',
                    'is_permission'     => false,
                    'check_in'          => '08:30:00',
                    'check_out'         => '12:30:00',
                    'remarks'           => 'Half Day afternoon leave',
                ]
            );
        } elseif ($day == 3 && $idx % 4 == 0) {
            StaffAttendance::updateOrCreate(
                ['employee_id' => $emp->id, 'date' => $date->toDateString()],
                [
                    'status'            => 'late',
                    'is_late'           => true,
                    'late_minutes'      => 25,
                    'is_permission'     => false,
                    'check_in'          => '08:55:00',
                    'check_out'         => '16:30:00',
                    'remarks'           => 'Traffic delay',
                ]
            );
        } else {
            StaffAttendance::updateOrCreate(
                ['employee_id' => $emp->id, 'date' => $date->toDateString()],
                [
                    'status'            => 'present',
                    'is_permission'     => false,
                    'check_in'          => '08:25:00',
                    'check_out'         => '16:30:00',
                    'remarks'           => null,
                ]
            );
        }
    }
}

echo "Seeding completed!\n";
