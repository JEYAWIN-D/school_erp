<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeExperience extends Model
{
    protected $fillable = [
        'employee_id', 'organisation', 'role', 'from_date', 'to_date',
        'is_current', 'reason_for_leaving', 'responsibilities', 'reference_contact',
    ];

    protected $casts = [
        'from_date'  => 'date',
        'to_date'    => 'date',
        'is_current' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getDurationAttribute(): string
    {
        $end   = $this->is_current ? now() : ($this->to_date ?? now());
        $years = (int) $this->from_date->diffInYears($end);
        $months = (int) $this->from_date->copy()->addYears($years)->diffInMonths($end);
        return $years > 0
            ? "{$years}y " . ($months > 0 ? "{$months}m" : '')
            : "{$months}m";
    }
}
