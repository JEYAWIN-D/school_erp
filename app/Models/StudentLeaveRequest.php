<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StudentLeaveRequest extends Model {
    protected $fillable = ['student_id','leave_type','from_date','to_date','days','reason','status','approved_by','remarks'];
    protected $casts = ['from_date'=>'date','to_date'=>'date'];
    public function student() { return $this->belongsTo(Student::class); }
    public function approvedBy() { return $this->belongsTo(User::class,'approved_by'); }
}
