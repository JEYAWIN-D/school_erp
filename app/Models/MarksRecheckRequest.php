<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarksRecheckRequest extends Model
{
    protected $fillable = [
        'exam_id', 'student_id', 'exam_schedule_id', 'marks_before', 'marks_after',
        'reason', 'status', 'admin_remarks', 'requested_by', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'marks_before' => 'decimal:2',
        'marks_after'  => 'decimal:2',
        'reviewed_at'  => 'datetime',
    ];

    public function exam(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function examSchedule(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExamSchedule::class);
    }

    public function requestedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'requested_by');
    }
}
