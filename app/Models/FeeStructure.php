<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    protected $fillable = [
        'class_id', 'section_id', 'fee_head_id', 'academic_year_id',
        'amount', 'frequency', 'due_date', 'is_optional', 'is_active',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'due_date'    => 'date',
        'is_optional' => 'boolean',
        'is_active'   => 'boolean',
    ];

    public function class(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function feeHead(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FeeHead::class);
    }

    public function academicYear(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
