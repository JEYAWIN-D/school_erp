<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCustomFee extends Model
{
    protected $fillable = [
        'student_id', 'fee_head_id', 'academic_year_id', 'custom_amount', 'reason', 'set_by',
    ];

    protected $casts = ['custom_amount' => 'decimal:2'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeHead()
    {
        return $this->belongsTo(FeeHead::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function setBy()
    {
        return $this->belongsTo(User::class, 'set_by');
    }
}
