<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\User;
use App\Models\Employee;
use App\Models\StaffAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

$admin = User::where('email', 'admin@schoolerp.in')->first() ?? User::first();
Auth::login($admin);

$emp = Employee::where('is_active', true)->first();
$today = today()->toDateString();

$postData = [
    '_token'        => 'test-token',
    'date'          => $today,
    'category'      => 'all',
    'department_id' => '',
    'attendance'    => [
        $emp->id => [
            'status'            => 'permission',
            'is_permission'     => '1',
            'permission_hours'  => '2.0',
            'permission_reason' => 'Bank Duty / Cheque Clearance',
            'in_time'           => '10:30',
            'out_time'          => '16:30',
        ]
    ]
];

$req = Request::create('/attendance/staff', 'POST', $postData);
$req->setUserResolver(fn() => $admin);
$controller = new App\Http\Controllers\Admin\AttendanceController();
$res = $controller->saveStaffAttendance($req);
echo "Save redirection: " . $res->getTargetUrl() . "\n";

$saved = StaffAttendance::where('employee_id', $emp->id)->where('date', $today)->first();
echo "Verified Saved Record:\n";
echo "ID: {$saved->id}\n";
echo "Status: {$saved->status}\n";
echo "Is Permission: " . ($saved->is_permission ? 'YES' : 'NO') . "\n";
echo "Hours: {$saved->permission_hours}h\n";
echo "Reason: {$saved->permission_reason}\n";
echo "Remarks: {$saved->remarks}\n";
