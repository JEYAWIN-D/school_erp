<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StaffAttendance extends Model {
    protected $table = 'staff_attendance';
    protected $fillable = [
        'employee_id', 'date', 'status', 'check_in', 'check_out', 'remarks',
        'is_late', 'late_minutes',
        'is_permission', 'permission_hours', 'permission_time', 'permission_reason'
    ];
    protected $casts = [
        'date' => 'date',
        'is_late' => 'boolean',
        'is_permission' => 'boolean',
        'permission_hours' => 'float',
    ];
    public function employee() { return $this->belongsTo(Employee::class); }
}
