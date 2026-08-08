<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostelRoom extends Model
{
    protected $fillable = [
        'hostel_id', 'room_number', 'room_type', 'capacity', 'floor',
        'is_ac', 'is_attached_bathroom', 'has_locker', 'fan_type', 'monthly_fee', 'status',
    ];

    protected $casts = [
        'is_ac'                => 'boolean',
        'is_attached_bathroom' => 'boolean',
        'has_locker'           => 'boolean',
        'monthly_fee'          => 'decimal:2',
    ];

    public function hostel(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    public function allotments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HostelAllotment::class);
    }

    public function beds(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HostelBed::class, 'room_id');
    }
}
