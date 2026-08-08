<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HostelOutpass extends Model
{
    protected $fillable = [
        'student_id', 'allotment_id', 'from_datetime', 'to_datetime',
        'reason', 'parent_contact', 'destination', 'status',
        'approved_by', 'approved_at', 'actual_return_time', 'created_by',
    ];

    protected $casts = [
        'from_datetime'       => 'datetime',
        'to_datetime'         => 'datetime',
        'approved_at'         => 'datetime',
        'actual_return_time'  => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function allotment(): BelongsTo
    {
        return $this->belongsTo(HostelAllotment::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
