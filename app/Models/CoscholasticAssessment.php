<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoscholasticAssessment extends Model
{
    protected $fillable = [
        'student_id', 'academic_year_id', 'term',
        'area', 'sub_area', 'grade', 'remarks', 'assessed_by',
    ];

    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function academicYear(): BelongsTo { return $this->belongsTo(AcademicYear::class); }
}
