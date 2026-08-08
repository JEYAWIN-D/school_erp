<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LmsCourse extends Model
{
    protected $fillable = ['title', 'subject_id', 'class_id', 'description', 'thumbnail', 'status', 'created_by'];

    public function units()
    {
        return $this->hasMany(LmsUnit::class, 'course_id')->orderBy('order');
    }

    public function lessons()
    {
        return $this->hasManyThrough(LmsLesson::class, LmsUnit::class, 'course_id', 'unit_id');
    }

    public function quizzes()
    {
        return $this->hasMany(LmsQuiz::class, 'course_id');
    }

    public function assignments()
    {
        return $this->hasMany(LmsAssignment::class, 'course_id');
    }

    public function discussions()
    {
        return $this->hasMany(LmsDiscussion::class, 'course_id')->where('is_hidden', false);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function classModel()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function progressFor(int $studentId): int
    {
        $totalLessons = $this->lessons()->where('is_published', true)->count();
        if ($totalLessons === 0) return 0;

        $completed = LmsLessonProgress::where('student_id', $studentId)
            ->whereIn('lesson_id', $this->lessons()->pluck('lms_lessons.id'))
            ->count();

        return (int) round($completed / $totalLessons * 100);
    }
}
