<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StudentApplication extends Model {
    protected $fillable = ['application_number','form_config_id','academic_year_id','class_id','enquiry_id',
        'form_data','documents','status','parent_name','parent_mobile','parent_email','student_name',
        'admin_notes','reviewed_by'];
    protected $casts = ['form_data'=>'array','documents'=>'array'];

    public function formConfig() { return $this->belongsTo(ApplicationFormConfig::class, 'form_config_id'); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function class() { return $this->belongsTo(Classes::class, 'class_id'); }
}
