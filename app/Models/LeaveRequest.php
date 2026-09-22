<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $fillable = [
        'employee_id', 'leave_type_id', 'from_date', 'to_date', 'total_days',
        'reason', 'status', 'applied_by', 'applied_on_behalf', 'approved_by', 'remarks',
        'is_half_day', 'half_day_session', 'attachment', 'approval_note', 'approved_at',
    ];

    protected $casts = [
        'from_date'         => 'date',
        'to_date'           => 'date',
        'applied_on_behalf' => 'boolean',
    ];

    public function getDaysAttribute(): ?float
    {
        return (float)($this->total_days ?? 0);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function appliedBy()
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
