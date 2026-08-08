<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransportStop extends Model
{
    protected $fillable = ['route_id', 'name', 'stop_order', 'distance_km', 'arrival_time', 'pickup_time', 'drop_time', 'fare', 'landmark'];

    public function route(): BelongsTo
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }
}
