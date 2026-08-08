<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeCarryForward extends Model
{
    protected $fillable = [
        'student_id', 'from_academic_year_id', 'to_academic_year_id',
        'outstanding_amount', 'recovered_amount', 'note', 'created_by',
    ];

    protected $casts = [
        'outstanding_amount' => 'float',
        'recovered_amount'   => 'float',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function fromYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'from_academic_year_id');
    }

    public function toYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'to_academic_year_id');
    }

    public function getRemainingAttribute(): float
    {
        return max(0, $this->outstanding_amount - $this->recovered_amount);
    }
}
