<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsAssignment extends Model
{
    protected $fillable = ['course_id', 'lesson_id', 'title', 'instructions', 'due_at', 'max_marks', 'attachment', 'created_by'];
    protected $casts = ['due_at' => 'datetime'];

    public function course()
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function submissions()
    {
        return $this->hasMany(LmsAssignmentSubmission::class, 'assignment_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOverdue(): bool
    {
        return now()->gt($this->due_at);
    }
}
