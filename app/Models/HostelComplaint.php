<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class HostelComplaint extends Model {
    protected $fillable = ['student_id','room_id','complaint_type','category','priority','description','status','assigned_to','vendor_name','reported_by','resolution_notes','resolved_at'];
    protected $casts = ['resolved_at'=>'datetime'];
    public function student() { return $this->belongsTo(Student::class); }
    public function room() { return $this->belongsTo(HostelRoom::class,'room_id'); }
    public function assignedTo() { return $this->belongsTo(Employee::class,'assigned_to'); }
    public function reportedBy() { return $this->belongsTo(\App\Models\User::class,'reported_by'); }
}
