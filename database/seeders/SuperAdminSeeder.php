<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = env('SUPERADMIN_DEFAULT_PASSWORD', 'Admin@1234');

        $user = User::firstOrCreate(
            ['email' => 'admin@schoolerp.in'],
            [
                'name'      => 'Super Admin',
                'mobile'    => '9999999999',
                'password'  => Hash::make($defaultPassword),
                'is_active' => true,
            ]
        );

        $user->assignRole('super_admin');

        if (!app()->isProduction()) {
            $this->command->info("Super Admin created: admin@schoolerp.in / {$defaultPassword}");
        } else {
            $this->command->info('Super Admin account verified.');
        }
    }
}
