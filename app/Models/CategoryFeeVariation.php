<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryFeeVariation extends Model
{
    protected $fillable = [
        'fee_structure_id', 'fee_head_id', 'student_category', 'amount', 'remarks',
    ];

    protected $casts = ['amount' => 'decimal:2'];

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function feeHead()
    {
        return $this->belongsTo(FeeHead::class);
    }
}
