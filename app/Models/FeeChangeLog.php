<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeChangeLog extends Model
{
    protected $fillable = [
        'student_id', 'class_id', 'fee_head_id', 'academic_year_id',
        'change_type', 'old_amount', 'new_amount', 'reason', 'changed_by',
    ];

    protected $casts = [
        'old_amount' => 'decimal:2',
        'new_amount' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function feeHead()
    {
        return $this->belongsTo(FeeHead::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
