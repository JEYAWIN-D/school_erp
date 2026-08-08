<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NightDutyStaff extends Model
{
    protected $table = 'night_duty_staff';

    protected $fillable = ['hostel_id', 'employee_id', 'duty_date', 'shift', 'notes', 'assigned_by'];

    protected $casts = ['duty_date' => 'date'];

    public function hostel(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function assignedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
