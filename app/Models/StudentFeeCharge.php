<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentFeeCharge extends Model
{
    protected $fillable = [
        'student_id', 'academic_year_id', 'fee_head_id',
        'amount', 'due_date', 'description',
        'source', 'source_id', 'is_active', 'created_by',
    ];

    protected $casts = [
        'amount'    => 'decimal:2',
        'due_date'  => 'date',
        'is_active' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feeHead(): BelongsTo
    {
        return $this->belongsTo(FeeHead::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
