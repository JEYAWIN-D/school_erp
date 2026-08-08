<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeCertification extends Model
{
    protected $fillable = [
        'employee_id', 'certification_name', 'issuing_authority',
        'issue_date', 'expiry_date', 'certificate_number', 'file_path',
    ];

    protected $casts = [
        'issue_date'  => 'date',
        'expiry_date' => 'date',
    ];

    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
