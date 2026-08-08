<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostelBed extends Model
{
    protected $fillable = ['room_id', 'bed_number', 'status', 'notes'];

    public function room()
    {
        return $this->belongsTo(HostelRoom::class, 'room_id');
    }

    public function allotment()
    {
        return $this->hasOne(HostelAllotment::class, 'bed_id')->where('status', 'active');
    }
}
