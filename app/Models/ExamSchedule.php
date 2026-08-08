<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSchedule extends Model
{
    protected $fillable = [
        'exam_id', 'class_id', 'subject_id', 'exam_date',
        'start_time', 'end_time', 'max_marks', 'pass_marks', 'venue',
    ];

    protected $casts = ['exam_date' => 'date'];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function marks()
    {
        return $this->hasMany(ExamMark::class);
    }
}
