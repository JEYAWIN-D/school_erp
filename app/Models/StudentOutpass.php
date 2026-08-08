<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentOutpass extends Model
{
    protected $fillable = [
        'student_id', 'pass_number', 'out_time', 'expected_return',
        'actual_return', 'reason', 'authorized_by', 'status', 'issued_by',
    ];

    protected $casts = [
        'out_time'        => 'datetime',
        'expected_return' => 'datetime',
        'actual_return'   => 'datetime',
    ];

    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function issuedBy(): BelongsTo { return $this->belongsTo(User::class, 'issued_by'); }

    protected static function booted(): void
    {
        static::saving(function ($p) {
            if ($p->expected_return && $p->status === 'active' && now()->gt($p->expected_return)) {
                $p->status = 'overdue';
            }
        });
    }
}
