<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransportStop extends Model
{
    protected $fillable = [
        'route_id',
        'vehicle_id',
        'van_number',
        'name',
        'stop_order',
        'distance_km',
        'arrival_time',
        'pickup_time',
        'drop_time',
        'fare',
        'landmark',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function getEffectiveVanNumberAttribute(): string
    {
        return $this->van_number 
            ?: ($this->vehicle?->vehicle_number 
            ?: ($this->route?->vehicle?->vehicle_number ?: ('Van #' . ($this->route_id ?: '1'))));
    }
}
