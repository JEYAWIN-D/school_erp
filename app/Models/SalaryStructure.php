<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryStructure extends Model
{
    protected $fillable = [
        'employee_id', 'basic_salary', 'hra', 'transport_allowance',
        'medical_allowance', 'other_allowances', 'pf_employee', 'pf_employer',
        'professional_tax', 'tds', 'effective_from',
    ];

    protected $casts = ['effective_from' => 'date'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getGrossSalaryAttribute(): float
    {
        return $this->basic_salary + $this->hra + $this->transport_allowance
            + $this->medical_allowance + $this->other_allowances;
    }

    public function getNetSalaryAttribute(): float
    {
        return $this->gross_salary - $this->pf_employee - $this->professional_tax - $this->tds;
    }
}
