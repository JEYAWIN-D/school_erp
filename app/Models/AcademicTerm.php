<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicTerm extends Model
{
    protected $fillable = ['academic_year_id', 'name', 'type', 'start_date', 'end_date', 'order_position'];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
