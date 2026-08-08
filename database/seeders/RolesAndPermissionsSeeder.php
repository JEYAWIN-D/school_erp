<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'view dashboard',

            // Admissions
            'view admissions', 'create admissions', 'edit admissions', 'delete admissions', 'export admissions',

            // Students
            'view students', 'create students', 'edit students', 'delete students', 'export students',

            // Academics
            'view academics', 'create academics', 'edit academics', 'delete academics',

            // Attendance
            'view attendance', 'mark attendance', 'edit attendance', 'export attendance',

            // Examinations
            'view examinations', 'create examinations', 'edit examinations', 'delete examinations',
            'enter marks', 'publish results', 'export results',

            // Fees
            'view fees', 'collect fees', 'edit fees', 'delete fees', 'export fees',
            'manage fee structure',

            // HR & Payroll
            'view employees', 'create employees', 'edit employees', 'delete employees',
            'process payroll', 'view payroll', 'export payroll',

            // Library
            'view library', 'manage library', 'issue books', 'return books',

            // Transport
            'view transport', 'manage transport',

            // Hostel
            'view hostel', 'manage hostel',

            // Communication
            'send sms', 'send whatsapp', 'send email', 'manage circulars',

            // Reports
            'view reports', 'export reports',

            // System Admin
            'manage users', 'manage roles', 'manage settings', 'view audit logs',

            // Parent/Student portal
            'access parent portal', 'access student portal',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Super Admin — all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        // School Admin
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::whereNotIn('name', [
            'manage roles', 'view audit logs',
        ])->get());

        // Principal
        $principal = Role::firstOrCreate(['name' => 'principal']);
        $principal->syncPermissions([
            'view dashboard', 'view admissions', 'view students', 'view academics',
            'view attendance', 'view examinations', 'publish results', 'view fees',
            'view employees', 'view payroll', 'view reports', 'export reports',
            'manage circulars', 'send sms', 'send whatsapp', 'send email',
        ]);

        // Accountant
        $accountant = Role::firstOrCreate(['name' => 'accountant']);
        $accountant->syncPermissions([
            'view dashboard', 'view fees', 'collect fees', 'edit fees',
            'manage fee structure', 'export fees', 'view reports', 'export reports',
            'process payroll', 'view payroll', 'export payroll',
        ]);

        // Teacher
        $teacher = Role::firstOrCreate(['name' => 'teacher']);
        $teacher->syncPermissions([
            'view dashboard', 'view students', 'view academics',
            'view attendance', 'mark attendance',
            'view examinations', 'enter marks',
            'manage circulars',
        ]);

        // Class Teacher (same base as teacher + extra)
        $classTeacher = Role::firstOrCreate(['name' => 'class_teacher']);
        $classTeacher->syncPermissions([
            'view dashboard', 'view students', 'edit students', 'view academics',
            'view attendance', 'mark attendance', 'edit attendance',
            'view examinations', 'enter marks',
            'manage circulars', 'send sms',
        ]);

        // Librarian
        $librarian = Role::firstOrCreate(['name' => 'librarian']);
        $librarian->syncPermissions([
            'view dashboard', 'view library', 'manage library', 'issue books', 'return books',
        ]);

        // Transport Manager
        $transport = Role::firstOrCreate(['name' => 'transport_manager']);
        $transport->syncPermissions(['view dashboard', 'view transport', 'manage transport']);

        // Hostel Warden
        $warden = Role::firstOrCreate(['name' => 'warden']);
        $warden->syncPermissions(['view dashboard', 'view hostel', 'manage hostel']);

        // HR Manager
        $hr = Role::firstOrCreate(['name' => 'hr_manager']);
        $hr->syncPermissions([
            'view dashboard', 'view employees', 'create employees', 'edit employees',
            'process payroll', 'view payroll', 'export payroll',
        ]);

        // Receptionist / Counsellor
        $reception = Role::firstOrCreate(['name' => 'receptionist']);
        $reception->syncPermissions([
            'view dashboard', 'view admissions', 'create admissions', 'edit admissions',
            'view students',
        ]);

        // Parent
        Role::firstOrCreate(['name' => 'parent']);

        // Student
        Role::firstOrCreate(['name' => 'student']);
    }
}
