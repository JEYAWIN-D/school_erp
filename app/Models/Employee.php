<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_code', 'first_name', 'last_name',
        'gender', 'dob', 'mobile', 'personal_email', 'official_email', 'email', 'address', 'residential_address',
        'photo', 'aadhaar_no', 'aadhaar_number', 'pan_no', 'pan_number',
        'department_id', 'designation_id', 'department', 'designation',
        'joining_date', 'employment_type', 'status', 'employee_type', 'is_active',
        'bank_name', 'bank_account_no', 'bank_account_number', 'bank_ifsc', 'bank_branch', 'pf_account_no', 'esi_no',
        'basic_salary', 'hra', 'ta', 'gross_salary',
        'qualification', 'experience_years',
        'emergency_contact_name', 'emergency_contact_mobile',
        'driver_name', 'driver_mobile', 'license_number', 'license_expiry',
        'assigned_vehicle', 'assigned_block', 'shift_timing',
        'salary_on_hold', 'investment_80c', 'investment_80d', 'hra_exemption', 'tax_regime',
        'manager_id',
    ];

    protected $casts = [
        'dob'            => 'date',
        'joining_date'   => 'date',
        'license_expiry' => 'date',
        'basic_salary'   => 'decimal:2',
        'hra'            => 'decimal:2',
        'ta'             => 'decimal:2',
        'gross_salary'   => 'decimal:2',
        'is_active'      => 'boolean',
        'aadhaar_no'      => \App\Casts\EncryptedStringResilient::class,
        'pan_no'          => \App\Casts\EncryptedStringResilient::class,
        'bank_account_no' => \App\Casts\EncryptedStringResilient::class,
    ];

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getEmployeeNumberAttribute(): ?string
    {
        return $this->employee_code;
    }

    public function getCategoryLabelAttribute(): string
    {
        return match (strtolower($this->employee_type ?? 'teaching')) {
            'teaching'     => 'Teaching Staff',
            'non_teaching' => 'Non-Teaching Staff',
            'driver'       => 'Driver',
            'cleaner'      => 'Cleaner',
            'nanny', 'naani' => 'Nanny (Naani)',
            default        => ucfirst(str_replace('_', ' ', $this->employee_type ?? 'Staff')),
        };
    }

    public function getCategoryBadgeClassAttribute(): string
    {
        return match (strtolower($this->employee_type ?? 'teaching')) {
            'teaching'     => 'badge-indigo',
            'non_teaching' => 'badge-purple',
            'driver'       => 'badge-amber',
            'cleaner'      => 'badge-teal',
            'nanny', 'naani' => 'badge-rose',
            default        => 'badge-blue',
        };
    }

    public function getDepartmentNameAttribute(): string
    {
        if (!empty($this->attributes['department'])) {
            return $this->attributes['department'];
        }
        return $this->department?->name ?? 'General';
    }

    public function getDesignationNameAttribute(): string
    {
        if (!empty($this->attributes['designation'])) {
            return $this->attributes['designation'];
        }
        return $this->designation?->name ?? 'Staff';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function designation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function leaveRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function payrollRecords(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PayrollRecord::class);
    }

    public function staffAttendances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StaffAttendance::class);
    }

    public function manager(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function directReports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    public function qualifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EmployeeQualification::class);
    }

    public function experiences(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EmployeeExperience::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
