<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsLessonProgress extends Model
{
    public $timestamps = false;
    protected $fillable = ['student_id', 'lesson_id', 'completed_at'];
    protected $casts = ['completed_at' => 'datetime'];
}
