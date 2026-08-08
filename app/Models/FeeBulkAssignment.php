<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeBulkAssignment extends Model
{
    protected $fillable = ['class_id', 'academic_year_id', 'assigned_by', 'student_count'];
}
