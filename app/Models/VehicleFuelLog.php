<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class VehicleFuelLog extends Model {
    protected $fillable = ['vehicle_id','log_date','quantity_litres','cost_per_litre','total_cost','odometer_reading','filled_by','remarks'];
    protected $casts = ['log_date'=>'date'];
    public function vehicle() { return $this->belongsTo(Vehicle::class); }
}
