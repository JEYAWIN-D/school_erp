<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsQuizAttempt extends Model
{
    protected $fillable = ['quiz_id', 'student_id', 'score', 'total_marks', 'started_at', 'submitted_at', 'status'];
    protected $casts = ['started_at' => 'datetime', 'submitted_at' => 'datetime'];

    public function quiz()
    {
        return $this->belongsTo(LmsQuiz::class, 'quiz_id');
    }

    public function answers()
    {
        return $this->hasMany(LmsQuizAnswer::class, 'attempt_id');
    }

    public function percentage(): float
    {
        if (!$this->total_marks) return 0;
        return round($this->score / $this->total_marks * 100, 1);
    }

    public function timeUsedMinutes(): int
    {
        if (!$this->submitted_at) return 0;
        return (int) $this->started_at->diffInMinutes($this->submitted_at);
    }
}
