<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $fillable = [
        'employee_id', 'leave_type_id', 'from_date', 'to_date', 'total_days',
        'days', 'reason', 'status', 'approved_by', 'remarks',
        'is_half_day', 'half_day_session', 'attachment', 'approval_note', 'approved_at',
    ];

    protected $casts = ['from_date' => 'date', 'to_date' => 'date'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
