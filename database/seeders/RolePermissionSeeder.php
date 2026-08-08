<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles/permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── 1. Permissions grouped by module ──────────────────────
        $allPermissions = [
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
            'manage fee structure', 'approve fees',
            // HR & Payroll
            'view employees', 'create employees', 'edit employees', 'delete employees',
            'process payroll', 'view payroll', 'export payroll',
            // Library
            'view library', 'manage library', 'issue books', 'return books',
            // Transport
            'view transport', 'manage transport',
            // Hostel
            'view hostel', 'manage hostel',
            // Events
            'view events', 'create events', 'edit events', 'delete events',
            // Gate & Visitors
            'view gate', 'manage gate',
            // Communication
            'send sms', 'send whatsapp', 'send email', 'manage circulars',
            // LMS
            'view lms', 'create lms', 'edit lms', 'delete lms',
            // Inventory
            'view inventory', 'manage inventory',
            // Alumni
            'view alumni', 'manage alumni',
            // Reports
            'view reports', 'export reports',
            // System Admin
            'manage users', 'manage roles', 'manage settings', 'view audit logs',
            // Portal
            'access parent portal', 'access student portal',
        ];

        foreach ($allPermissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // ── 2. Role definitions ────────────────────────────────────
        $allPerms = Permission::pluck('name')->toArray();

        $systemPerms = ['manage users', 'manage roles', 'manage settings', 'view audit logs'];
        $feePerms    = ['view fees', 'collect fees', 'edit fees', 'delete fees', 'export fees', 'manage fee structure', 'approve fees'];
        $hrPerms     = ['view employees', 'create employees', 'edit employees', 'delete employees', 'process payroll', 'view payroll', 'export payroll'];
        $libPerms    = ['view library', 'manage library', 'issue books', 'return books'];
        $transPerms  = ['view transport', 'manage transport'];
        $hostelPerms = ['view hostel', 'manage hostel'];
        $eventPerms  = ['view events', 'create events', 'edit events', 'delete events'];
        $gatePerms   = ['view gate', 'manage gate'];
        $commsPerms  = ['send sms', 'send whatsapp', 'send email', 'manage circulars'];
        $lmsPerms    = ['view lms', 'create lms', 'edit lms', 'delete lms'];
        $invPerms    = ['view inventory', 'manage inventory'];
        $alumniPerms = ['view alumni', 'manage alumni'];
        $reportPerms = ['view reports', 'export reports'];

        $roles = [
            // Tier 1 — Management
            'owner' => $allPerms,

            'super_admin' => $allPerms,

            'admin' => $allPerms,

            'principal' => array_diff($allPerms, ['manage roles', 'manage settings', 'access student portal', 'access parent portal']),

            'vice_principal' => [
                'view dashboard',
                'view admissions', 'view students', 'edit students',
                'view academics', 'create academics', 'edit academics',
                'view attendance', 'mark attendance', 'edit attendance', 'export attendance',
                'view examinations', 'publish results',
                'view employees',
                ...$eventPerms,
                'send email', 'manage circulars',
                'view reports',
            ],

            // Tier 2 — Department Staff
            'hod' => [
                'view dashboard',
                'view students', 'view academics', 'create academics', 'edit academics',
                'view attendance', 'export attendance',
                'view examinations', 'enter marks',
                'view employees',
                'view lms', 'create lms', 'edit lms',
                'view reports',
            ],

            'class_teacher' => [
                'view dashboard',
                'view students',
                'view academics',
                'view attendance', 'mark attendance', 'edit attendance',
                'view examinations', 'enter marks',
                'view events',
                'manage circulars',
                'view lms',
            ],

            'teacher' => [
                'view dashboard',
                'view students',
                'view academics',
                'view attendance', 'mark attendance', 'edit attendance',
                'view examinations', 'enter marks',
                'view events',
                'manage circulars',
                'view lms', 'create lms', 'edit lms',
            ],

            'subject_teacher' => [
                'view dashboard',
                'view students',
                'view academics',
                'view attendance', 'mark attendance',
                'view examinations', 'enter marks',
                'view lms', 'create lms', 'edit lms',
            ],

            'accountant' => [
                'view dashboard',
                ...$feePerms,
                'view payroll', 'export payroll',
                'view reports', 'export reports',
            ],

            'hr_manager' => [
                'view dashboard',
                ...$hrPerms,
                'view reports', 'export reports',
            ],

            'librarian' => [
                'view dashboard',
                ...$libPerms,
                'view reports',
            ],

            'transport_manager' => [
                'view dashboard',
                ...$transPerms,
                'view reports',
            ],

            'hostel_warden' => [
                'view dashboard',
                ...$hostelPerms,
                'view reports',
            ],

            'warden' => [
                'view dashboard',
                ...$hostelPerms,
                'view reports',
            ],

            'admission_counsellor' => [
                'view dashboard',
                'view admissions', 'create admissions', 'edit admissions', 'export admissions',
                'view students',
                'view reports',
            ],

            'receptionist' => [
                'view dashboard',
                'view admissions',
                ...$gatePerms,
                'view events',
                'manage circulars',
                'view reports',
            ],

            'inventory_manager' => [
                'view dashboard',
                ...$invPerms,
                'view reports',
            ],

            'event_coordinator' => [
                'view dashboard',
                ...$eventPerms,
                'send email', 'manage circulars',
                'view reports',
            ],

            'it_admin' => [
                'view dashboard',
                ...$systemPerms,
                ...$commsPerms,
                'view reports', 'export reports',
            ],

            'alumni_coordinator' => [
                'view dashboard',
                ...$alumniPerms,
                'send email', 'manage circulars',
                'view reports',
            ],

            // Tier 3 — Portal users
            'parent' => ['access parent portal'],

            'student' => ['access student portal'],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions(array_unique($perms));
        }

        $this->command->info('Roles & permissions seeded: ' . count($roles) . ' roles, ' . count($allPermissions) . ' permissions.');
    }
}
