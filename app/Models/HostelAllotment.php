<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostelAllotment extends Model
{
    protected $fillable = [
        'student_id', 'hostel_id', 'room_id', 'academic_year_id',
        'allotment_date', 'vacating_date', 'vacate_reason', 'vacated_by', 'status',
        'admission_fee', 'security_deposit', 'monthly_fee', 'remarks',
        'mess_included', 'mess_exclusion_reason',
        'transferred_from_room_id', 'transfer_date', 'transfer_reason',
    ];

    protected $casts = [
        'allotment_date'   => 'date',
        'vacating_date'    => 'date',
        'admission_fee'    => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'mess_included'    => 'boolean',
    ];

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function hostel(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    public function room(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(HostelRoom::class, 'room_id');
    }

    public function academicYear(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function outpasses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HostelOutpass::class, 'allotment_id');
    }
}
