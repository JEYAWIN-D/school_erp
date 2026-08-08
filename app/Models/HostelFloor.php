<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostelFloor extends Model
{
    protected $fillable = ['hostel_id', 'name', 'floor_number'];

    public function hostel(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    public function rooms(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\HostelRoom::class, 'floor_id');
    }
}
