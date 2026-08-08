<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsQuiz extends Model
{
    protected $fillable = ['lesson_id', 'course_id', 'title', 'duration_minutes', 'marks_per_question', 'negative_marks', 'available_from', 'available_to', 'randomise'];
    protected $casts = [
        'available_from' => 'datetime',
        'available_to'   => 'datetime',
        'randomise'      => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function questions()
    {
        return $this->hasMany(LmsQuizQuestion::class, 'quiz_id')->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(LmsQuizAttempt::class, 'quiz_id');
    }

    public function isAvailable(): bool
    {
        $now = now();
        if ($this->available_from && $now->lt($this->available_from)) return false;
        if ($this->available_to   && $now->gt($this->available_to))   return false;
        return true;
    }
}
