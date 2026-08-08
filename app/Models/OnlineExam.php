<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnlineExam extends Model
{
    protected $fillable = [
        'title', 'academic_year_id', 'class_id', 'subject_id',
        'start_time', 'end_time', 'duration_minutes', 'total_marks', 'pass_marks',
        'randomise_questions', 'negative_marking', 'negative_marks_per_wrong',
        'status', 'instructions',
    ];

    protected $casts = [
        'start_time'            => 'datetime',
        'end_time'              => 'datetime',
        'randomise_questions'   => 'boolean',
        'negative_marking'      => 'boolean',
        'total_marks'           => 'decimal:2',
        'pass_marks'            => 'decimal:2',
        'negative_marks_per_wrong' => 'decimal:2',
    ];

    public function class(): BelongsTo   { return $this->belongsTo(Classes::class, 'class_id'); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function academicYear(): BelongsTo { return $this->belongsTo(AcademicYear::class); }
    public function examQuestions(): HasMany { return $this->hasMany(OnlineExamQuestion::class); }
    public function attempts(): HasMany { return $this->hasMany(OnlineExamAttempt::class); }

    public function isLive(): bool
    {
        return $this->status === 'published' && now()->between($this->start_time, $this->end_time);
    }
}
