<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PrincipalUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure permissions exist
        $permissions = [
            'view dashboard',
            'view admissions',
            'create admissions',
            'edit admissions',
            'approve admissions',
            'view students',
            'edit students',
            'view academics',
            'view attendance',
            'view examinations',
            'publish results',
            'view fees',
            'view employees',
            'view payroll',
            'view reports',
            'export reports',
        ];

        foreach ($permissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        // 2. Ensure Role exists and has permissions
        $principalRole = Role::firstOrCreate(['name' => 'principal', 'guard_name' => 'web']);
        $principalRole->syncPermissions($permissions);

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::all());

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminRole->givePermissionTo(Permission::all());

        // 3. Create or update Principal user
        $principal = User::where('email', 'principal@schoolerp.in')->first();
        if (!$principal) {
            $principal = User::create([
                'name'      => 'Dr. K. S. Ramanathan (Principal)',
                'email'     => 'principal@schoolerp.in',
                'mobile'    => '9840112233',
                'password'  => Hash::make('Demo@2026'),
                'is_active' => true,
            ]);
        } else {
            $principal->update([
                'name'      => 'Dr. K. S. Ramanathan (Principal)',
                'password'  => Hash::make('Demo@2026'),
                'is_active' => true,
            ]);
        }

        if (!$principal->hasRole('principal')) {
            $principal->assignRole('principal');
        }

        // 4. Ensure super_admin user (admin@schoolerp.in) exists with super_admin role
        $admin = User::where('email', 'admin@schoolerp.in')->first();
        if ($admin && !$admin->hasRole('super_admin')) {
            $admin->assignRole('super_admin');
        }

        echo "Principal user seeded successfully: principal@schoolerp.in / Demo@2026\n";
    }
}
