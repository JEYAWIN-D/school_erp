<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsUnit extends Model
{
    protected $fillable = ['course_id', 'title', 'order'];

    public function course()
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function lessons()
    {
        return $this->hasMany(LmsLesson::class, 'unit_id')->orderBy('order');
    }
}
