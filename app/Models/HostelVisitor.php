<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HostelVisitor extends Model {
    protected $fillable = [
        'student_id', 'hostel_id', 'visitor_name', 'visitor_mobile', 'visitor_phone',
        'id_type', 'id_number', 'visitor_photo', 'relation', 'purpose', 'destination',
        'vehicle_number', 'entry_time', 'exit_time', 'in_time', 'out_time',
        'registered_by', 'logged_by', 'pass_token', 'pass_valid_until',
    ];

    protected $casts = [
        'entry_time'       => 'datetime',
        'exit_time'        => 'datetime',
        'pass_valid_until' => 'datetime',
    ];

    public function student()  { return $this->belongsTo(Student::class); }
    public function hostel()   { return $this->belongsTo(Hostel::class); }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (!$model->pass_token) {
                $model->pass_token = Str::random(32);
            }
            if (!$model->pass_valid_until) {
                $model->pass_valid_until = now()->addHours(2);
            }
        });
    }

    public function isPassValid(): bool
    {
        return $this->pass_valid_until && now()->lt($this->pass_valid_until) && !$this->exit_time;
    }
}
