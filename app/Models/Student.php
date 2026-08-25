<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'admission_no', 'admission_date', 'roll_number',
        'first_name', 'middle_name', 'last_name', 'dob', 'gender',
        'blood_group', 'religion', 'caste', 'sub_caste', 'category',
        'nationality', 'mother_tongue', 'aadhaar_no',
        'passport_number', 'passport_expiry',
        'mobile', 'email', 'photo',
        'residential_address', 'permanent_address', 'pincode',
        'is_disabled', 'disability_description',
        'annual_family_income',
        'father_name', 'father_mobile', 'father_occupation', 'father_email', 'father_aadhaar',
        'mother_name', 'mother_mobile', 'mother_occupation', 'mother_email',
        'guardian_name', 'guardian_mobile', 'guardian_relation',
        'emergency_contact_name', 'emergency_contact_mobile',
        'status', 'student_type', 'sibling_group_id', 'parent_employee_id',
        'leaving_date', 'leaving_reason',
        'allergies', 'medical_conditions',
        'previous_school_name', 'previous_school_board', 'tc_number', 'tc_date',
        'previous_percentage', 'migration_certificate_number', 'migration_certificate_date',
        'tc_document', 'marksheet_document', 'migration_document',
        'scholarship_name', 'scholarship_amount', 'scholarship_sanction_letter',
        'portal_blocked', 'portal_block_reason', 'portal_blocked_at',
        'payment_terms', 'total_admission_fee', 'admission_paid_amount',
        'admission_pending_amount', 'payment_mode', 'payment_date', 'payment_status', 'admission_fee_terms',
    ];

    protected $casts = [
        'dob'            => 'date',
        'admission_date' => 'date',
        'tc_date'        => 'date',
        'leaving_date'   => 'date',
        'payment_date'   => 'date',
        'is_disabled'       => 'boolean',
        'portal_blocked'    => 'boolean',
        'portal_blocked_at' => 'datetime',
        'annual_family_income'          => 'decimal:2',
        'migration_certificate_date'    => 'date',
        'passport_expiry'               => 'date',
        'previous_percentage'           => 'decimal:2',
        'total_admission_fee'           => 'decimal:2',
        'admission_paid_amount'         => 'decimal:2',
        'admission_pending_amount'      => 'decimal:2',
        'admission_fee_terms'           => 'array',
    ];

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name);
    }

    public function getAdmissionNumberAttribute(): ?string
    {
        return $this->attributes['admission_no'] ?? null;
    }

    public function getAadhaarNumberAttribute(): ?string
    {
        return $this->attributes['aadhaar_no'] ?? null;
    }

    public function getRollNoAttribute(): ?string
    {
        return $this->attributes['roll_number'] ?? null;
    }

    public function siblings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Student::class, 'sibling_group_id', 'sibling_group_id')
            ->where('id', '!=', $this->id);
    }

    public function enrollments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function currentEnrollment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StudentEnrollment::class)
            ->where('status', 'active')
            ->latest();
    }

    public function parentEmployee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class, 'parent_employee_id');
    }

    public function bookIssues(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\BookIssue::class);
    }

    public function feePayments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\FeePayment::class);
    }
}
