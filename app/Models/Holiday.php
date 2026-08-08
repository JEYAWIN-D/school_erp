<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'name', 'date', 'end_date', 'type', 'description', 'academic_year_id',
    ];

    protected $casts = [
        'date'     => 'date',
        'end_date' => 'date',
    ];

    public function academicYear(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
