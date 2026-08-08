<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlineExamQuestion extends Model
{
    protected $fillable = ['online_exam_id', 'question_bank_id', 'sort_order'];

    public function exam(): BelongsTo     { return $this->belongsTo(OnlineExam::class, 'online_exam_id'); }
    public function question(): BelongsTo { return $this->belongsTo(QuestionBank::class, 'question_bank_id'); }
}
