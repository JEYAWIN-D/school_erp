<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class VehicleMaintenance extends Model {
    protected $fillable = ['vehicle_id','maintenance_type','service_date','odometer','cost','vendor','description','work_done','next_service_date','remarks'];
    protected $casts = ['service_date'=>'date','next_service_date'=>'date'];
    public function vehicle() { return $this->belongsTo(Vehicle::class); }
}
