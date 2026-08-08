<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HostelFeeStructure extends Model
{
    protected $fillable = [
        'hostel_id', 'room_type', 'academic_year_id',
        'hostel_id', 'room_type', 'academic_year_id',
        'monthly_fee', 'admission_fee', 'mess_fee', 'security_deposit',
        'mess_included_default',
    ];

    protected $casts = [
        'monthly_fee'            => 'decimal:2',
        'admission_fee'          => 'decimal:2',
        'mess_fee'               => 'decimal:2',
        'security_deposit'       => 'decimal:2',
        'mess_included_default'  => 'boolean',
    ];

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
