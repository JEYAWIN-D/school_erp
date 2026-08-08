<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArrearsBonusEntry extends Model
{
    protected $fillable = [
        'employee_id', 'type', 'amount', 'month', 'description', 'payment_mode', 'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
