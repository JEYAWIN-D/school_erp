<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'student_id', 'class_id', 'section_id', 'academic_year_id',
        'date', 'status', 'remark', 'marked_by',
        'period_data', 'arrival_time', 'departure_time',
        'is_late', 'cutoff_override', 'cutoff_override_by',
    ];

    protected $casts = [
        'date'            => 'date',
        'period_data'     => 'array',
        'is_late'         => 'boolean',
        'cutoff_override' => 'boolean',
    ];

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function enrollment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class);
    }

    public function section(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function markedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function academicYear(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function class(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Classes::class, 'class_id');
    }
}
