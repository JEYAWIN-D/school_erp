<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'vehicle_number', 'make', 'model', 'vehicle_type',
        'seating_capacity', 'driver_name', 'driver_mobile', 'driver_license',
        'route_id', 'status', 'is_active',
        'fitness_expiry', 'insurance_expiry', 'permit_expiry', 'puc_expiry', 'tax_expiry',
        'gps_enabled', 'gps_device_id', 'gps_imei', 'gps_status',
        'current_latitude', 'current_longitude', 'current_location_name',
        'current_speed_kmh', 'battery_level', 'ignition_status', 'last_gps_ping',
    ];

    protected $casts = [
        'fitness_expiry'   => 'date',
        'insurance_expiry' => 'date',
        'permit_expiry'    => 'date',
        'puc_expiry'       => 'date',
        'tax_expiry'       => 'date',
        'gps_enabled'      => 'boolean',
        'current_latitude' => 'float',
        'current_longitude'=> 'float',
        'current_speed_kmh'=> 'integer',
        'battery_level'    => 'integer',
        'last_gps_ping'    => 'datetime',
    ];

    public function getGpsStatusBadgeAttribute(): array
    {
        return match($this->gps_status) {
            'in_transit' => ['bg' => 'bg-blue-100 text-blue-800 border-blue-200', 'label' => 'In Transit (' . $this->current_speed_kmh . ' km/h)', 'dot' => 'bg-blue-500'],
            'online'     => ['bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'label' => 'Online & Ready', 'dot' => 'bg-emerald-500'],
            'idle'       => ['bg' => 'bg-amber-100 text-amber-800 border-amber-200', 'label' => 'Idle / Parked', 'dot' => 'bg-amber-500'],
            default      => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'label' => 'Offline', 'dot' => 'bg-slate-400'],
        };
    }

    public function route(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }

    public function maintenanceLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(VehicleMaintenance::class);
    }

    public function fuelLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(VehicleFuelLog::class);
    }
}
