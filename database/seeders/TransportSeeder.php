<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransportSeeder extends Seeder
{
    public function run(): void
    {
        // Vehicles
        $vehicleIds = [];
        $vehicles = [
            ['vehicle_number' => 'MH-12-AA-1001', 'vehicle_type' => 'Bus', 'make' => 'Tata Motors', 'model' => 'Starbus', 'seating_capacity' => 40, 'is_active' => true],
            ['vehicle_number' => 'MH-12-AA-1002', 'vehicle_type' => 'Bus', 'make' => 'Ashok Leyland', 'model' => 'Lynx', 'seating_capacity' => 35, 'is_active' => true],
            ['vehicle_number' => 'MH-12-AA-1003', 'vehicle_type' => 'Van', 'make' => 'Force Motors', 'model' => 'Traveller', 'seating_capacity' => 16, 'is_active' => true],
        ];
        foreach ($vehicles as $v) {
            $existing = DB::table('vehicles')->where('vehicle_number', $v['vehicle_number'])->first();
            if (!$existing) {
                $vehicleIds[] = DB::table('vehicles')->insertGetId(array_merge($v, ['created_at' => now(), 'updated_at' => now()]));
            } else {
                $vehicleIds[] = $existing->id;
            }
        }

        // Routes with route_number, distance, fee
        $routes = [
            [
                'route_name'    => 'Route 1 — North Campus Express',
                'route_number'  => 'R-01',
                'from_location' => 'Main Campus Gate',
                'to_location'   => 'Anna Nagar Roundtana',
                'distance_km'   => 15.5,
                'fee'           => 14000,
                'is_active'     => true,
            ],
            [
                'route_name'    => 'Route 2 — South City Shuttle',
                'route_number'  => 'R-02',
                'from_location' => 'Main Campus Gate',
                'to_location'   => 'Tambaram Junction',
                'distance_km'   => 18.0,
                'fee'           => 16000,
                'is_active'     => true,
            ],
            [
                'route_name'    => 'Route 3 — Central Ring Road',
                'route_number'  => 'R-03',
                'from_location' => 'Main Campus Gate',
                'to_location'   => 'T. Nagar Bus Terminus',
                'distance_km'   => 12.0,
                'fee'           => 12000,
                'is_active'     => true,
            ],
        ];

        $routeIds = [];
        foreach ($routes as $r) {
            $existing = DB::table('transport_routes')->where('route_name', $r['route_name'])->first();
            if (!$existing) {
                $routeIds[] = DB::table('transport_routes')->insertGetId(array_merge($r, ['created_at' => now(), 'updated_at' => now()]));
            } else {
                DB::table('transport_routes')->where('id', $existing->id)->update(array_merge($r, ['updated_at' => now()]));
                $routeIds[] = $existing->id;
            }
        }

        // Detailed Stops for Route 1 (Stopping, Km, Landmark, Fare)
        if (!empty($routeIds[0])) {
            $stops1 = [
                ['name' => 'Campus Main Gate',          'stop_order' => 1, 'distance_km' => 0.0,  'fare' => 0,     'landmark' => 'School Entrance',         'pickup_time' => '07:30', 'drop_time' => '16:00'],
                ['name' => 'Shenoy Nagar Metro Stop',   'stop_order' => 2, 'distance_km' => 4.2,  'fare' => 6500,  'landmark' => 'Near Metro Station Gate 2', 'pickup_time' => '07:45', 'drop_time' => '15:45'],
                ['name' => 'Kilpauk Water Tank',        'stop_order' => 3, 'distance_km' => 8.0,  'fare' => 9500,  'landmark' => 'Opp. Medical College',     'pickup_time' => '08:00', 'drop_time' => '15:30'],
                ['name' => 'Chetpet Overbridge',        'stop_order' => 4, 'distance_km' => 11.5, 'fare' => 12000, 'landmark' => 'Near Railway Crossing',     'pickup_time' => '08:15', 'drop_time' => '15:15'],
                ['name' => 'Anna Nagar Roundtana',      'stop_order' => 5, 'distance_km' => 15.5, 'fare' => 14000, 'landmark' => 'Near Main Clock Tower',     'pickup_time' => '08:30', 'drop_time' => '15:00'],
            ];
            foreach ($stops1 as $stop) {
                $exists = DB::table('transport_stops')->where('route_id', $routeIds[0])->where('name', $stop['name'])->first();
                if (!$exists) {
                    DB::table('transport_stops')->insert(array_merge($stop, ['route_id' => $routeIds[0], 'created_at' => now(), 'updated_at' => now()]));
                } else {
                    DB::table('transport_stops')->where('id', $exists->id)->update(array_merge($stop, ['updated_at' => now()]));
                }
            }
        }

        // Detailed Stops for Route 2
        if (!empty($routeIds[1])) {
            $stops2 = [
                ['name' => 'Guindy Industrial Estate', 'stop_order' => 1, 'distance_km' => 5.0,  'fare' => 7500,  'landmark' => 'Near Kathipara Junction', 'pickup_time' => '07:35', 'drop_time' => '15:55'],
                ['name' => 'Saidapet Court Junction',  'stop_order' => 2, 'distance_km' => 9.5,  'fare' => 11000, 'landmark' => 'Opp. Sub-Jail Road',      'pickup_time' => '07:50', 'drop_time' => '15:40'],
                ['name' => 'Chromepet Main Bus Stop',  'stop_order' => 3, 'distance_km' => 14.0, 'fare' => 13500, 'landmark' => 'Near MIT Bridge Gate',     'pickup_time' => '08:05', 'drop_time' => '15:25'],
                ['name' => 'Tambaram Junction Terminal','stop_order' => 4, 'distance_km' => 18.0, 'fare' => 16000, 'landmark' => 'West Bus Stand Entrance',  'pickup_time' => '08:20', 'drop_time' => '15:10'],
            ];
            foreach ($stops2 as $stop) {
                $exists = DB::table('transport_stops')->where('route_id', $routeIds[1])->where('name', $stop['name'])->first();
                if (!$exists) {
                    DB::table('transport_stops')->insert(array_merge($stop, ['route_id' => $routeIds[1], 'created_at' => now(), 'updated_at' => now()]));
                } else {
                    DB::table('transport_stops')->where('id', $exists->id)->update(array_merge($stop, ['updated_at' => now()]));
                }
            }
        }

        // Detailed Stops for Route 3
        if (!empty($routeIds[2])) {
            $stops3 = [
                ['name' => 'Nungambakkam High Road',    'stop_order' => 1, 'distance_km' => 3.5,  'fare' => 6000,  'landmark' => 'Near Taj Coromandel',    'pickup_time' => '07:40', 'drop_time' => '15:50'],
                ['name' => 'Kodambakkam Bridge',        'stop_order' => 2, 'distance_km' => 7.0,  'fare' => 9000,  'landmark' => 'Near Liberty Theatre',   'pickup_time' => '07:55', 'drop_time' => '15:35'],
                ['name' => 'T. Nagar Bus Terminus',     'stop_order' => 3, 'distance_km' => 12.0, 'fare' => 12000, 'landmark' => 'Usman Road Flyover',    'pickup_time' => '08:15', 'drop_time' => '15:15'],
            ];
            foreach ($stops3 as $stop) {
                $exists = DB::table('transport_stops')->where('route_id', $routeIds[2])->where('name', $stop['name'])->first();
                if (!$exists) {
                    DB::table('transport_stops')->insert(array_merge($stop, ['route_id' => $routeIds[2], 'created_at' => now(), 'updated_at' => now()]));
                } else {
                    DB::table('transport_stops')->where('id', $exists->id)->update(array_merge($stop, ['updated_at' => now()]));
                }
            }
        }

        // Fuel logs (last 30 days)
        if (!empty($vehicleIds)) {
            $hasFuel = DB::table('vehicle_fuel_logs')->whereIn('vehicle_id', $vehicleIds)->exists();
            if (!$hasFuel) {
                $fuelRows = [];
                foreach ($vehicleIds as $vId) {
                    for ($d = 30; $d >= 1; $d -= 7) {
                        $litres = round(rand(50, 120) + rand(0, 99) / 100, 2);
                        $rate   = 95.50;
                        $fuelRows[] = [
                            'vehicle_id'      => $vId,
                            'log_date'        => now()->subDays($d)->toDateString(),
                            'quantity_litres' => $litres,
                            'cost_per_litre'  => $rate,
                            'total_cost'      => round($litres * $rate, 2),
                            'odometer_reading'=> rand(15000, 90000),
                            'filled_by'       => 'Driver',
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];
                    }
                }
                DB::table('vehicle_fuel_logs')->insert($fuelRows);
            }
        }

        // Maintenance records
        if (!empty($vehicleIds)) {
            $hasMaint = DB::table('vehicle_maintenances')->whereIn('vehicle_id', $vehicleIds)->exists();
            if (!$hasMaint) {
                $maintRows = [];
                $types = ['oil_change', 'tyre_rotation', 'brake_check', 'engine_service'];
                foreach ($vehicleIds as $i => $vId) {
                    $maintRows[] = [
                        'vehicle_id'       => $vId,
                        'service_date'     => now()->subDays(rand(10, 90))->toDateString(),
                        'maintenance_type' => $types[$i % count($types)],
                        'description'      => 'Routine ' . str_replace('_', ' ', $types[$i % count($types)]),
                        'cost'             => rand(2000, 15000),
                        'vendor'           => 'Authorized Service Center',
                        'next_service_date'=> now()->addDays(rand(30, 180))->toDateString(),
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];
                }
                DB::table('vehicle_maintenances')->insert($maintRows);
            }
        }
    }
}
