<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlineExamResponse extends Model
{
    protected $fillable = [
        'attempt_id', 'question_bank_id', 'answer',
        'is_correct', 'marks_awarded', 'evaluator_remarks', 'evaluated_by',
    ];

    protected $casts = [
        'is_correct'    => 'boolean',
        'marks_awarded' => 'decimal:2',
    ];

    public function attempt(): BelongsTo  { return $this->belongsTo(OnlineExamAttempt::class); }
    public function question(): BelongsTo { return $this->belongsTo(QuestionBank::class, 'question_bank_id'); }
}
