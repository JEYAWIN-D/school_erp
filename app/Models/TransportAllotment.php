<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportAllotment extends Model
{
    protected $fillable = [
        'student_id', 'enrollment_id', 'route_id', 'stop_id', 'vehicle_id',
        'fee', 'pickup_time', 'drop_time', 'boarding_stop',
        'pickup_point', 'monthly_fee', 'allotment_date', 'status', 'is_active',
        'academic_year_id',
    ];

    protected $casts = ['allotment_date' => 'date'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function route()
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }
}
