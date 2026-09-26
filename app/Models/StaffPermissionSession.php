<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class StaffPermissionSession extends Model
{
    protected $table = 'staff_permission_sessions';

    protected $fillable = [
        'staff_attendance_id',
        'session_order',
        'out_time',
        'in_time',
        'in_time_auto_filled',
    ];

    protected $casts = [
        'in_time_auto_filled' => 'boolean',
        'session_order'       => 'integer',
    ];

    public function staffAttendance()
    {
        return $this->belongsTo(StaffAttendance::class, 'staff_attendance_id');
    }

    public function employee()
    {
        return $this->hasOneThrough(Employee::class, StaffAttendance::class, 'id', 'id', 'staff_attendance_id', 'employee_id');
    }

    public function getFormattedOutTimeAttribute(): ?string
    {
        if (empty($this->out_time)) {
            return null;
        }
        try {
            return Carbon::parse($this->out_time)->format('h:i A');
        } catch (\Exception $e) {
            return $this->out_time;
        }
    }

    public function getFormattedInTimeAttribute(): ?string
    {
        if (empty($this->in_time)) {
            return null;
        }
        try {
            return Carbon::parse($this->in_time)->format('h:i A');
        } catch (\Exception $e) {
            return $this->in_time;
        }
    }

    public function getTimingDisplayAttribute(): string
    {
        $out = $this->formatted_out_time ?? '—';
        if (!empty($this->in_time)) {
            $in = $this->formatted_in_time;
            if ($this->in_time_auto_filled) {
                $in .= ' (Dispersal)';
            }
            return "{$out} - {$in}";
        }
        return "{$out} - Not Entered";
    }
}
