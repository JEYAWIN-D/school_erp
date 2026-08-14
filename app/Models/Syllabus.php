<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Syllabus extends Model
{
    protected $table = 'syllabus';

    protected $fillable = [
        'class_id',
        'subject_id',
        'academic_year_id',
        'chapter_number',
        'chapter_title',
        'topics',
        'description',
        'status',
        'planned_date',
        'completed_date',
        'sort_order',
        'term',
        'document_path',
    ];

    protected $casts = [
        'planned_date'   => 'date',
        'completed_date' => 'date',
        'sort_order'     => 'integer',
    ];

    /* ── Relationships ─────────────────────────────────────── */

    public function class(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function subject(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /* ── Accessors ─────────────────────────────────────────── */

    /**
     * Unified "title" accessor — supports both old `topic` column and new `chapter_title`.
     */
    public function getTitleAttribute(): string
    {
        return $this->chapter_title ?? $this->attributes['topic'] ?? '—';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'completed'   => 'badge-green',
            'in_progress' => 'badge-amber',
            default       => 'badge-slate',
        };
    }

    /* ── Scopes ────────────────────────────────────────────── */

    public function scopeForClass($query, int $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeForSubject($query, int $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeForYear($query, int $yearId)
    {
        return $query->where('academic_year_id', $yearId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('chapter_number');
    }
}
