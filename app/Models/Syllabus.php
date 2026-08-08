<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Syllabus extends Model
{
    protected $table = 'syllabus';

    protected $fillable = [
        'class_id', 'subject_id', 'topic', 'description',
        'status', 'sort_order', 'academic_year_id',
        'term', 'document_path',
    ];

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
