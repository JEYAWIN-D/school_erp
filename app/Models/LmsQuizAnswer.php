<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsQuizAnswer extends Model
{
    public $timestamps = false;
    protected $fillable = ['attempt_id', 'question_id', 'given_answer', 'is_correct', 'marks_awarded'];

    public function question()
    {
        return $this->belongsTo(LmsQuizQuestion::class, 'question_id');
    }
}
