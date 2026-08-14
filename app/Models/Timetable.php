<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    protected $fillable = [
        'class_id', 'section_id', 'subject_id', 'teacher_id', 'employee_id',
        'day', 'day_of_week', 'period_number', 'start_time', 'end_time',
        'room', 'is_active', 'period_type', 'effective_from', 'academic_year_id',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'is_active' => 'boolean',
    ];

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Employee::class, 'teacher_id');
    }

    // Mutator for employee_id compatibility
    public function setEmployeeIdAttribute($value)
    {
        $this->attributes['teacher_id'] = $value;
    }
}

