<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TeacherSubjectAllocation extends Model {
    protected $fillable = ['employee_id','subject_id','class_id','section_id','academic_year_id'];
    public function employee() { return $this->belongsTo(Employee::class); }
    public function subject() { return $this->belongsTo(Subject::class); }
    public function class() { return $this->belongsTo(Classes::class,'class_id'); }
    public function section() { return $this->belongsTo(Section::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
}
