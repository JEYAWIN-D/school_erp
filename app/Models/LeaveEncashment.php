<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveEncashment extends Model
{
    protected $fillable = [
        'employee_id', 'leave_type_id', 'days_encashed',
        'basic_per_day', 'amount', 'year', 'remarks',
        'processed_by', 'encashment_date',
    ];

    protected $casts = ['encashment_date' => 'date', 'amount' => 'float', 'basic_per_day' => 'float'];

    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }
}
