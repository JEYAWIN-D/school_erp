<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OvertimeEntry extends Model
{
    protected $fillable = [
        'employee_id', 'entry_date', 'hours', 'rate_per_hour', 'amount', 'month', 'remarks', 'created_by',
    ];

    protected $casts = [
        'entry_date'    => 'date',
        'hours'         => 'decimal:2',
        'rate_per_hour' => 'decimal:2',
        'amount'        => 'decimal:2',
    ];

    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
