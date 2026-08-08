<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolSettingsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('school_settings')->insertOrIgnore([
            'school_name'       => 'Demo School',
            'school_code'       => 'SCH001',
            'affiliation_no'    => '',
            'board'             => 'CBSE',
            'address'           => '123, Main Street',
            'city'              => 'Chennai',
            'state'             => 'Tamil Nadu',
            'pincode'           => '600001',
            'phone'             => '044-12345678',
            'email'             => 'admin@demoschool.in',
            'website'           => 'https://demoschool.in',
            'principal_name'    => 'Dr. S. Ramesh',
            'primary_color'     => '#3B82F6',
            'currency_symbol'   => '₹',
            'date_format'       => 'd/m/Y',
            'timezone'          => 'Asia/Kolkata',
            'medium'            => 'English',
            'sms_enabled'       => false,
            'whatsapp_enabled'  => false,
            'online_payment_enabled' => false,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        DB::table('academic_years')->insertOrIgnore([
            ['name' => '2024-2025', 'start_date' => '2024-04-01', 'end_date' => '2025-03-31', 'is_current' => false, 'is_locked' => true,  'created_at' => now(), 'updated_at' => now()],
            ['name' => '2025-2026', 'start_date' => '2025-04-01', 'end_date' => '2026-03-31', 'is_current' => true,  'is_locked' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $classes = [
            ['name' => 'LKG',  'numeric_value' => 0,  'sort_order' => 1],
            ['name' => 'UKG',  'numeric_value' => 0,  'sort_order' => 2],
            ['name' => 'I',    'numeric_value' => 1,  'sort_order' => 3],
            ['name' => 'II',   'numeric_value' => 2,  'sort_order' => 4],
            ['name' => 'III',  'numeric_value' => 3,  'sort_order' => 5],
            ['name' => 'IV',   'numeric_value' => 4,  'sort_order' => 6],
            ['name' => 'V',    'numeric_value' => 5,  'sort_order' => 7],
            ['name' => 'VI',   'numeric_value' => 6,  'sort_order' => 8],
            ['name' => 'VII',  'numeric_value' => 7,  'sort_order' => 9],
            ['name' => 'VIII', 'numeric_value' => 8,  'sort_order' => 10],
            ['name' => 'IX',   'numeric_value' => 9,  'sort_order' => 11],
            ['name' => 'X',    'numeric_value' => 10, 'sort_order' => 12],
            ['name' => 'XI',   'numeric_value' => 11, 'sort_order' => 13],
            ['name' => 'XII',  'numeric_value' => 12, 'sort_order' => 14],
        ];
        foreach ($classes as $c) {
            DB::table('classes')->insertOrIgnore(array_merge($c, [
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
