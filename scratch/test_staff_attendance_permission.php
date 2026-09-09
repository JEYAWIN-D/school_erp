<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;
use App\Models\StaffAttendance;
use Illuminate\Support\Facades\DB;

echo "=== Testing Staff Attendance Permission ===\n";

// Find an active employee
$emp = Employee::where('is_active', true)->first();
if (!$emp) {
    die("No active employee found!\n");
}
echo "Active Employee: {$emp->full_name} (ID: {$emp->id})\n";

$today = today()->toDateString();

// Seed or update attendance for this employee with permission
$att = StaffAttendance::updateOrCreate(
    [
        'employee_id' => $emp->id,
        'date'        => $today,
    ],
    [
        'status'            => 'permission',
        'is_permission'     => true,
        'permission_hours'  => 1.5,
        'permission_time'   => '09:00 - 10:30 AM',
        'permission_reason' => 'Doctor Appointment',
        'check_in'          => '10:30:00',
        'check_out'         => '16:30:00',
        'remarks'           => 'Approved Permission (1.5h) — Doctor Appointment',
    ]
);

echo "Saved StaffAttendance ID: {$att->id}\n";
echo "Status: {$att->status}\n";
echo "Is Permission: " . ($att->is_permission ? 'YES' : 'NO') . "\n";
echo "Hours: {$att->permission_hours}h\n";
echo "Reason: {$att->permission_reason}\n";

// Count today's permissions
$permCount = StaffAttendance::where('date', $today)->where('status', 'permission')->count();
echo "Total today permissions: {$permCount}\n";

echo "=== Done ===\n";
