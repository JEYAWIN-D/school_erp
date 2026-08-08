<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StudentDocument extends Model {
    protected $fillable = ['student_id','document_type','file_path','original_name','status','verified_by','verified_at','remarks','expiry_date','reminder_sent'];
    protected $casts = ['verified_at'=>'datetime','expiry_date'=>'date','reminder_sent'=>'boolean'];
    public function student() { return $this->belongsTo(Student::class); }
    public function verifiedBy() { return $this->belongsTo(User::class,'verified_by'); }
}
