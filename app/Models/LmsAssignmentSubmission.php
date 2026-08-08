<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsAssignmentSubmission extends Model
{
    protected $fillable = ['assignment_id', 'student_id', 'file_path', 'note', 'submitted_at', 'is_late', 'score', 'feedback', 'evaluated_at', 'evaluated_by'];
    protected $casts = ['submitted_at' => 'datetime', 'evaluated_at' => 'datetime', 'is_late' => 'boolean'];

    public function assignment()
    {
        return $this->belongsTo(LmsAssignment::class, 'assignment_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}
