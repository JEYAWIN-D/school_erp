<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLearningRecord extends Model
{
    protected $fillable = [
        'student_id', 'academic_year_id', 'activity_name', 'activity_type',
        'description', 'activity_date', 'outcome', 'attachment', 'recorded_by',
    ];

    protected $casts = ['activity_date' => 'date'];

    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function academicYear(): BelongsTo { return $this->belongsTo(AcademicYear::class); }
}
