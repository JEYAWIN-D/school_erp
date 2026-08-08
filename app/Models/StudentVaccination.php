<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StudentVaccination extends Model {
    protected $fillable = ['student_id','vaccine_name','date_given','dose'];
    protected $casts = ['date_given'=>'date'];
    public function student() { return $this->belongsTo(Student::class); }
}
