<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StudentMedicalRecord extends Model {
    protected $fillable = ['student_id','allergies','chronic_conditions','medications','family_doctor','doctor_mobile','nearest_hospital','health_insurance_no'];
    public function student() { return $this->belongsTo(Student::class); }
    public function vaccinations() { return $this->hasMany(StudentVaccination::class,'student_id','student_id'); }
}
