<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLoan extends Model
{
    protected $fillable = [
        'employee_id', 'loan_type', 'principal_amount', 'interest_rate',
        'emi_months', 'emi_amount', 'total_paid', 'outstanding_balance',
        'disbursement_date', 'emi_start_month', 'status', 'purpose',
        'approved_by', 'approved_at',
    ];

    protected $casts = [
        'principal_amount'    => 'decimal:2',
        'interest_rate'       => 'decimal:2',
        'emi_amount'          => 'decimal:2',
        'total_paid'          => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
        'disbursement_date'   => 'date',
        'emi_start_month'     => 'date',
        'approved_at'         => 'datetime',
    ];

    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }
}
