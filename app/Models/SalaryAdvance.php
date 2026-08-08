<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryAdvance extends Model
{
    protected $fillable = [
        'employee_id', 'amount', 'advance_date', 'reason', 'status',
        'recovery_months', 'monthly_deduction', 'recovered_amount',
        'approved_by', 'approved_date', 'remarks',
    ];

    protected $casts = [
        'advance_date'  => 'date',
        'approved_date' => 'date',
        'amount'        => 'float',
        'monthly_deduction' => 'float',
        'recovered_amount'  => 'float',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function getRemainingAttribute(): float
    {
        return max(0, $this->amount - $this->recovered_amount);
    }
}
