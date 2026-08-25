<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name', 'code', 'class_id', 'type', 'max_marks', 'pass_marks',
        'is_theory', 'is_practical', 'is_active', 'sort_order',
        'is_elective', 'language_type', 'board_curriculum', 'credit_hours',
        'stream', 'medium', 'is_coscholastic',
    ];

    protected $casts = [
        'is_theory'       => 'boolean',
        'is_practical'    => 'boolean',
        'is_active'       => 'boolean',
        'is_elective'     => 'boolean',
        'is_coscholastic' => 'boolean',
    ];

    public function class(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function examSchedules(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ExamSchedule::class);
    }

    public function allocations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TeacherSubjectAllocation::class, 'subject_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
