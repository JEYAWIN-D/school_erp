<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetencyAssessment extends Model
{
    protected $fillable = [
        'student_id', 'competency_id', 'academic_year_id', 'term',
        'level', 'remarks', 'assessed_by', 'assessment_date',
    ];

    protected $casts = ['assessment_date' => 'date'];

    public function student(): BelongsTo    { return $this->belongsTo(Student::class); }
    public function competency(): BelongsTo { return $this->belongsTo(Competency::class); }
    public function academicYear(): BelongsTo { return $this->belongsTo(AcademicYear::class); }

    public function getLevelLabelAttribute(): string
    {
        return match($this->level) {
            'achieved'           => 'Achieved',
            'partially_achieved' => 'Partially Achieved',
            'not_achieved'       => 'Not Achieved',
            default              => 'Not Assessed',
        };
    }

    public function getLevelColorAttribute(): string
    {
        return match($this->level) {
            'achieved'           => 'badge-green',
            'partially_achieved' => 'badge-amber',
            'not_achieved'       => 'badge-red',
            default              => 'badge-slate',
        };
    }
}
