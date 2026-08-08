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

        // Routes (transport_routes has: route_name, from_location, to_location, fee, is_active — no vehicle_id)
        $routes = [
            ['route_name' => 'Route A — North Zone', 'from_location' => 'School', 'to_location' => 'North Zone', 'is_active' => true, 'fee' => 1200],
            ['route_name' => 'Route B — South Zone', 'from_location' => 'School', 'to_location' => 'South Zone', 'is_active' => true, 'fee' => 1000],
            ['route_name' => 'Route C — East Zone',  'from_location' => 'School', 'to_location' => 'East Zone',  'is_active' => true, 'fee' => 800],
        ];
        $routeIds = [];
        foreach ($routes as $r) {
            $existing = DB::table('transport_routes')->where('route_name', $r['route_name'])->first();
            if (!$existing) {
                $routeIds[] = DB::table('transport_routes')->insertGetId(array_merge($r, ['created_at' => now(), 'updated_at' => now()]));
            } else {
                $routeIds[] = $existing->id;
            }
        }

        // Stops for Route A (transport_stops has: route_id, name, stop_order)
        if (!empty($routeIds[0])) {
            $stopsA = ['Main Gate', 'Market Square', 'Railway Station', 'City Park', 'Hospital Junction'];
            foreach ($stopsA as $i => $stop) {
                $exists = DB::table('transport_stops')->where('route_id', $routeIds[0])->where('name', $stop)->exists();
                if (!$exists) {
                    DB::table('transport_stops')->insert(['route_id' => $routeIds[0], 'name' => $stop, 'stop_order' => $i + 1, 'created_at' => now(), 'updated_at' => now()]);
                }
            }
        }

        // Stops for Route B
        if (!empty($routeIds[1])) {
            $stopsB = ['School Gate', 'Bus Stand', 'Garden Road', 'Old Town'];
            foreach ($stopsB as $i => $stop) {
                $exists = DB::table('transport_stops')->where('route_id', $routeIds[1])->where('name', $stop)->exists();
                if (!$exists) {
                    DB::table('transport_stops')->insert(['route_id' => $routeIds[1], 'name' => $stop, 'stop_order' => $i + 1, 'created_at' => now(), 'updated_at' => now()]);
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
