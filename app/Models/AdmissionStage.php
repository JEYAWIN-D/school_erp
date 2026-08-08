<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AdmissionStage extends Model {
    protected $fillable = ['enquiry_id','stage','status','scheduled_date','scheduled_time','venue','score','remarks','done_by','completed_at'];
    protected $casts = ['scheduled_date'=>'date','completed_at'=>'datetime'];
    public function enquiry() { return $this->belongsTo(Enquiry::class); }
}
