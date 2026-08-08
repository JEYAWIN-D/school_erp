<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollRecord extends Model
{
    protected $fillable = [
        'employee_id', 'academic_year_id', 'month', 'year',
        'basic_salary', 'hra', 'ta', 'other_allowances',
        'gross_salary', 'overtime_amount', 'arrears_amount', 'bonus_amount',
        'pf_deduction', 'esi_deduction', 'tds_deduction',
        'other_deductions', 'net_salary',
        'present_days', 'absent_days', 'leave_days', 'working_days',
        'payment_date', 'payment_mode', 'transaction_reference',
        'status', 'generated_by', 'paid_by',
        'is_locked', 'locked_at', 'approved_by',
        'lop_days', 'lop_amount', 'loan_emi',
    ];

    protected $casts = [
        'payment_date'     => 'date',
        'basic_salary'     => 'decimal:2',
        'hra'              => 'decimal:2',
        'ta'               => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'gross_salary'     => 'decimal:2',
        'overtime_amount'  => 'decimal:2',
        'arrears_amount'   => 'decimal:2',
        'bonus_amount'     => 'decimal:2',
        'pf_deduction'     => 'decimal:2',
        'esi_deduction'    => 'decimal:2',
        'tds_deduction'    => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'net_salary'       => 'decimal:2',
        'lop_amount'       => 'decimal:2',
        'loan_emi'         => 'decimal:2',
        'is_locked'        => 'boolean',
        'locked_at'        => 'datetime',
    ];

    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function generatedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
