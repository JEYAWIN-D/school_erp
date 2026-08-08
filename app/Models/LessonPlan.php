<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonPlan extends Model
{
    protected $fillable = [
        'syllabus_id', 'class_id', 'subject_id', 'academic_year_id',
        'topic', 'learning_objectives', 'teaching_method', 'resources_required',
        'period_number', 'plan_date', 'duration_minutes', 'teacher_notes',
        'status', 'created_by', 'hod_reviewed_by', 'hod_reviewed_at',
        'hod_remarks', 'hod_decision', 'is_completed', 'completed_at',
    ];

    protected $casts = [
        'plan_date'       => 'date',
        'hod_reviewed_at' => 'datetime',
        'completed_at'    => 'datetime',
        'is_completed'    => 'boolean',
    ];

    public function class(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function subject(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function createdBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function hodReviewedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'hod_reviewed_by');
    }

    public function syllabus(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Syllabus::class);
    }
}
