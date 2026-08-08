<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Substitution extends Model
{
    protected $fillable = [
        'date', 'absent_teacher_id', 'substitute_teacher_id',
        'class_id', 'section_id', 'subject_id', 'period_number',
        'start_time', 'end_time', 'remarks', 'arranged_by',
    ];

    protected $casts = ['date' => 'date'];

    public function absentTeacher()     { return $this->belongsTo(Employee::class, 'absent_teacher_id'); }
    public function substituteTeacher() { return $this->belongsTo(Employee::class, 'substitute_teacher_id'); }
    public function class()    { return $this->belongsTo(Classes::class, 'class_id'); }
    public function section()  { return $this->belongsTo(Section::class, 'section_id'); }
    public function subject()  { return $this->belongsTo(Subject::class, 'subject_id'); }
    public function arrangedBy(){ return $this->belongsTo(User::class, 'arranged_by'); }
}
