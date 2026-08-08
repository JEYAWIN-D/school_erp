<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    protected $fillable = [
        'name', 'type', 'capacity', 'warden_id', 'address', 'contact', 'is_active',
        'warden_name', 'warden_mobile', 'total_capacity', 'meal_menus',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function warden(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class, 'warden_id');
    }

    public function wardens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\HostelWardenRoster::class, 'hostel_id');
    }

    public function rooms(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HostelRoom::class);
    }

    public function allotments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HostelAllotment::class);
    }
}
