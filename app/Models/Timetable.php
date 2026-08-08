<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    protected $fillable = [
        'class_id', 'section_id', 'subject_id', 'employee_id',
        'day', 'day_of_week', 'period_number', 'start_time', 'end_time',
        'period_type', 'effective_from', 'academic_year_id',
    ];

    protected $casts = [
        'effective_from' => 'date',
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
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
