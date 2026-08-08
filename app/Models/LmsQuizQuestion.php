<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsQuizQuestion extends Model
{
    protected $fillable = ['quiz_id', 'question', 'type', 'options', 'correct_answer', 'marks', 'order'];
    protected $casts = ['options' => 'array'];

    public function quiz()
    {
        return $this->belongsTo(LmsQuiz::class, 'quiz_id');
    }

    public function isCorrect(string $given): bool
    {
        return trim(strtolower($given)) === trim(strtolower($this->correct_answer));
    }
}
