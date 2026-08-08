<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StudentDisciplinary extends Model {
    protected $fillable = [
        'student_id','incident_date','incident_type','description','action_taken',
        'action_type','suspension_from','suspension_to','award_name',
        'reported_by','parent_notified','parent_notified_at',
    ];
    protected $casts = [
        'incident_date'      => 'date',
        'suspension_from'    => 'date',
        'suspension_to'      => 'date',
        'parent_notified'    => 'boolean',
        'parent_notified_at' => 'datetime',
    ];
    public function student() { return $this->belongsTo(Student::class); }
    public function reportedBy() { return $this->belongsTo(User::class,'reported_by'); }
}
