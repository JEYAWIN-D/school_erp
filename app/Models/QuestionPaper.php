<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionPaper extends Model
{
    protected $fillable = [
        'exam_id', 'subject_id', 'class_id', 'title', 'file_path', 'original_name',
        'accessible_from', 'is_restricted', 'uploaded_by',
    ];

    protected $casts = [
        'accessible_from' => 'date',
        'is_restricted'   => 'boolean',
    ];

    public function exam(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function subject(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function class(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function isAccessible(): bool
    {
        if (!$this->is_restricted) return true;
        return $this->accessible_from && $this->accessible_from->isPast();
    }
}
