<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSchedule extends Model
{
    protected $fillable = [
        'exam_id', 'class_id', 'subject_id', 'exam_date',
        'start_time', 'end_time', 'total_marks', 'passing_marks',
        'max_marks', 'pass_marks', 'room', 'venue', 'grace_marks',
    ];

    protected $casts = ['exam_date' => 'date'];

    public function getMaxMarksAttribute()
    {
        return $this->attributes['total_marks'] ?? $this->attributes['max_marks'] ?? 100;
    }

    public function setMaxMarksAttribute($value)
    {
        $this->attributes['total_marks'] = $value;
    }

    public function getPassMarksAttribute()
    {
        return $this->attributes['passing_marks'] ?? $this->attributes['pass_marks'] ?? 35;
    }

    public function setPassMarksAttribute($value)
    {
        $this->attributes['passing_marks'] = $value;
    }

    public function getVenueAttribute()
    {
        return $this->attributes['room'] ?? $this->attributes['venue'] ?? null;
    }

    public function setVenueAttribute($value)
    {
        $this->attributes['room'] = $value;
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function marks()
    {
        return $this->hasMany(ExamMark::class);
    }
}
