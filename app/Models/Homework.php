<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    protected $fillable = [
        'class_id', 'section_id', 'subject_id', 'employee_id',
        'title', 'description', 'due_date', 'assigned_date',
        'max_score', 'assigned_by', 'is_active', 'attachment',
    ];

    protected $casts = ['due_date' => 'date', 'assigned_date' => 'date'];

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function submissions()
    {
        return $this->hasMany(HomeworkSubmission::class);
    }
}
