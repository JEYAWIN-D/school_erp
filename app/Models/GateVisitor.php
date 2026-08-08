<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GateVisitor extends Model
{
    protected $fillable = [
        'visitor_name', 'visitor_phone', 'visitor_id_type', 'visitor_id_number',
        'purpose', 'whom_to_meet', 'department', 'vehicle_number',
        'pass_token', 'visitor_photo', 'in_time', 'out_time',
        'is_approved', 'is_blacklisted', 'logged_by', 'remarks',
    ];

    protected $casts = [
        'in_time'  => 'datetime',
        'out_time' => 'datetime',
        'is_approved'    => 'boolean',
        'is_blacklisted' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($v) {
            $v->pass_token = $v->pass_token ?? Str::random(32);
            $v->in_time    = $v->in_time ?? now();
        });
    }

    public function loggedBy(): BelongsTo { return $this->belongsTo(User::class, 'logged_by'); }

    public function isInside(): bool { return is_null($this->out_time); }
}
