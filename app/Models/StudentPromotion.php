<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StudentPromotion extends Model {
    protected $fillable = ['student_id','from_class_id','to_class_id','from_academic_year_id','to_academic_year_id','status','promoted_by','remarks','promoted_at'];
    protected $casts = ['promoted_at'=>'datetime'];
    public function student() { return $this->belongsTo(Student::class); }
    public function fromClass() { return $this->belongsTo(Classes::class,'from_class_id'); }
    public function toClass() { return $this->belongsTo(Classes::class,'to_class_id'); }
}
