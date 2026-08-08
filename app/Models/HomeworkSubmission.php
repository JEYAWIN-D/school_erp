<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeworkSubmission extends Model
{
    protected $fillable = [
        'homework_id', 'student_id', 'status',
        'submitted_at', 'file_path', 'student_remarks',
        'score', 'teacher_feedback', 'evaluated_by', 'evaluated_at',
    ];

    protected $casts = [
        'submitted_at'  => 'date',
        'evaluated_at'  => 'datetime',
        'score'         => 'decimal:2',
    ];

    public function homework()
    {
        return $this->belongsTo(Homework::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function evaluatedBy()
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}
