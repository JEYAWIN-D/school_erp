<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCondonation extends Model
{
    protected $fillable = ['student_id', 'academic_year_id', 'days_condoned', 'reason', 'condoned_by', 'condoned_on'];

    protected $casts = ['condoned_on' => 'date'];

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class);
    }

    public function condonedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'condoned_by');
    }
}
