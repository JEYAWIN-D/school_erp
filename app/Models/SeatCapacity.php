<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SeatCapacity extends Model {
    protected $fillable = ['class_id','academic_year_id','category','total_seats','filled_seats'];
    public function class() { return $this->belongsTo(Classes::class,'class_id'); }
    public function getAvailableSeatsAttribute(): int { return max(0, $this->total_seats - $this->filled_seats); }
}
