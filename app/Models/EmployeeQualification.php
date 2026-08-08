<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeQualification extends Model
{
    protected $fillable = [
        'employee_id', 'degree', 'subject', 'institution', 'university',
        'year_of_passing', 'grade_or_percentage', 'education_level',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
