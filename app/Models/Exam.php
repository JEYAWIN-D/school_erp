<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'name', 'academic_year_id', 'type', 'start_date', 'end_date',
        'status', 'grading_scheme_id', 'is_published', 'supplementary_threshold',
        'marks_locked', 'marks_locked_at', 'marks_locked_by',
        'best_of_n_subjects',
        'term_label', 'weightage_percent', 'is_cumulative_component',
        'is_external', 'conducting_body',
    ];

    protected $casts = [
        'start_date'               => 'date',
        'end_date'                 => 'date',
        'is_published'             => 'boolean',
        'marks_locked'             => 'boolean',
        'marks_locked_at'          => 'datetime',
        'weightage_percent'        => 'decimal:2',
        'is_cumulative_component'  => 'boolean',
        'is_external'              => 'boolean',
    ];

    public function academicYear(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function gradingScheme(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(GradingScheme::class);
    }

    public function schedules(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ExamSchedule::class);
    }

    public function marks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ExamMark::class);
    }
}
