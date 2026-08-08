<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StudentConcession extends Model {
    protected $fillable = ['student_id','scholarship_id','academic_year_id','concession_type','value_type','value','applicable_fee_heads','valid_from','valid_to','granted_by','remarks'];
    protected $casts = ['valid_from'=>'date','valid_to'=>'date','applicable_fee_heads'=>'array'];
    public function student() { return $this->belongsTo(Student::class); }
    public function scholarship() { return $this->belongsTo(ScholarshipScheme::class,'scholarship_id'); }
}
