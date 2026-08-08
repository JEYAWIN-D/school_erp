<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Owner Account',        'email' => 'owner@schoolerp.in',               'role' => 'owner'],
            ['name' => 'School Admin',          'email' => 'schooladmin@schoolerp.in',          'role' => 'admin'],
            ['name' => 'Principal Demo',        'email' => 'principal@schoolerp.in',            'role' => 'principal'],
            ['name' => 'Vice Principal Demo',   'email' => 'viceprincipal@schoolerp.in',        'role' => 'vice_principal'],
            ['name' => 'HOD Demo',              'email' => 'hod@schoolerp.in',                  'role' => 'hod'],
            ['name' => 'Class Teacher Demo',    'email' => 'classteacher@schoolerp.in',         'role' => 'class_teacher'],
            ['name' => 'Teacher Demo',          'email' => 'teacher@schoolerp.in',              'role' => 'teacher'],
            ['name' => 'Subject Teacher Demo',  'email' => 'subjectteacher@schoolerp.in',       'role' => 'subject_teacher'],
            ['name' => 'Accountant Demo',       'email' => 'accountant@schoolerp.in',           'role' => 'accountant'],
            ['name' => 'HR Manager Demo',       'email' => 'hr@schoolerp.in',                   'role' => 'hr_manager'],
            ['name' => 'Librarian Demo',        'email' => 'librarian@schoolerp.in',            'role' => 'librarian'],
            ['name' => 'Transport Manager',     'email' => 'transport@schoolerp.in',            'role' => 'transport_manager'],
            ['name' => 'Hostel Warden Demo',    'email' => 'hostelwarden@schoolerp.in',         'role' => 'hostel_warden'],
            ['name' => 'Warden Demo',           'email' => 'warden@schoolerp.in',               'role' => 'warden'],
            ['name' => 'Admission Counsellor',  'email' => 'admissions@schoolerp.in',           'role' => 'admission_counsellor'],
            ['name' => 'Receptionist Demo',     'email' => 'reception@schoolerp.in',            'role' => 'receptionist'],
            ['name' => 'Inventory Manager',     'email' => 'inventory@schoolerp.in',            'role' => 'inventory_manager'],
            ['name' => 'Event Coordinator',     'email' => 'events@schoolerp.in',               'role' => 'event_coordinator'],
            ['name' => 'IT Admin Demo',         'email' => 'itadmin@schoolerp.in',              'role' => 'it_admin'],
            ['name' => 'Alumni Coordinator',    'email' => 'alumni@schoolerp.in',               'role' => 'alumni_coordinator'],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'      => $data['name'],
                    'password'  => Hash::make('Demo@2026'),
                    'is_active' => true,
                ]
            );
            $user->syncRoles([$data['role']]);
            $this->command->info("OK {$data['email']} -> {$data['role']}");
        }
    }
}
