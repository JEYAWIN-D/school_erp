<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusAttendance extends Model
{
    protected $table    = 'bus_attendance';
    protected $fillable = [
        'vehicle_id', 'route_id', 'date', 'trip_type',
        'student_id', 'status', 'boarding_stop', 'remarks', 'marked_by',
    ];

    protected $casts = ['date' => 'date'];

    public function vehicle() { return $this->belongsTo(Vehicle::class); }
    public function route()   { return $this->belongsTo(TransportRoute::class); }
    public function student() { return $this->belongsTo(Student::class); }
}
