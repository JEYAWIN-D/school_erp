<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnlineExamAttempt extends Model
{
    protected $fillable = [
        'online_exam_id', 'student_id', 'started_at', 'submitted_at',
        'auto_submitted', 'score', 'negative_marks', 'final_score',
        'result', 'question_order',
    ];

    protected $casts = [
        'started_at'     => 'datetime',
        'submitted_at'   => 'datetime',
        'auto_submitted' => 'boolean',
        'question_order' => 'array',
        'score'          => 'decimal:2',
        'negative_marks' => 'decimal:2',
        'final_score'    => 'decimal:2',
    ];

    public function exam(): BelongsTo    { return $this->belongsTo(OnlineExam::class, 'online_exam_id'); }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function responses(): HasMany { return $this->hasMany(OnlineExamResponse::class, 'attempt_id'); }
}
