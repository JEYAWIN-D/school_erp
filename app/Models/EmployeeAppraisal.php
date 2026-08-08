<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAppraisal extends Model
{
    protected $fillable = [
        'employee_id', 'appraisal_year', 'ratings', 'overall_score',
        'rating_label', 'hod_remarks', 'principal_remarks', 'appraised_by', 'appraised_at',
        'self_ratings', 'self_score', 'self_remarks', 'self_submitted_at',
        'increment_amount', 'increment_percent', 'increment_effective_date',
        'increment_approved_by', 'increment_approved_at',
    ];

    protected $casts = [
        'ratings'                => 'array',
        'self_ratings'           => 'array',
        'overall_score'          => 'decimal:2',
        'self_score'             => 'decimal:2',
        'increment_amount'       => 'decimal:2',
        'increment_percent'      => 'decimal:2',
        'increment_effective_date'=> 'date',
        'appraised_at'           => 'datetime',
        'self_submitted_at'      => 'datetime',
        'increment_approved_at'  => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function appraisedBy()
    {
        return $this->belongsTo(User::class, 'appraised_by');
    }
}
