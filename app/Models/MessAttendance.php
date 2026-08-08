<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessAttendance extends Model
{
    protected $table = 'mess_attendance';

    protected $fillable = ['allotment_id', 'date', 'meal', 'is_present'];

    protected $casts = [
        'date'       => 'date',
        'is_present' => 'boolean',
    ];

    public function allotment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(HostelAllotment::class);
    }
}
