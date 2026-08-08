<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamMark extends Model
{
    protected $fillable = [
        'exam_schedule_id', 'student_id', 'marks_obtained',
        'grade', 'remarks', 'is_absent',
    ];

    protected $casts = ['is_absent' => 'boolean', 'marks_obtained' => 'float'];

    public function examSchedule()
    {
        return $this->belongsTo(ExamSchedule::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
