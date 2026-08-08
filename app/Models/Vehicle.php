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
    ];

    protected $casts = [
        'fitness_expiry'   => 'date',
        'insurance_expiry' => 'date',
        'permit_expiry'    => 'date',
        'puc_expiry'       => 'date',
        'tax_expiry'       => 'date',
    ];

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
