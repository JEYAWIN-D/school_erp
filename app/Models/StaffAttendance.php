<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StaffAttendance extends Model {
    protected $fillable = ['employee_id','date','status','check_in','check_out','remarks','is_late','late_minutes'];
    protected $casts = ['date'=>'date','is_late'=>'boolean'];
    public function employee() { return $this->belongsTo(Employee::class); }
}
