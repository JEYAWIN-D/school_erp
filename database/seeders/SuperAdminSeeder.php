<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@schoolerp.in'],
            [
                'name'      => 'Super Admin',
                'mobile'    => '9999999999',
                'password'  => Hash::make('Admin@1234'),
                'is_active' => true,
            ]
        );

        $user->assignRole('super_admin');

        $this->command->info('Super Admin created: admin@schoolerp.in / Admin@1234');
    }
}
