<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class BookReservation extends Model {
    protected $fillable = ['book_id','student_id','employee_id','queue_position','status','reserved_at','expires_at'];
    protected $casts = ['reserved_at'=>'datetime','expires_at'=>'datetime'];
    public function book() { return $this->belongsTo(Book::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function employee() { return $this->belongsTo(Employee::class); }
}
