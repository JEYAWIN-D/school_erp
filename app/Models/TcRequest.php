<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TcRequest extends Model
{
    protected $fillable = [
        'student_id', 'requested_by_type', 'requested_by_user', 'reason', 'status',
        'hod_approved_by', 'hod_approved_at', 'principal_approved_by', 'principal_approved_at',
        'issued_by', 'issued_at', 'rejection_reason', 'rejected_by',
    ];

    protected $casts = [
        'hod_approved_at'       => 'datetime',
        'principal_approved_at' => 'datetime',
        'issued_at'             => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function requestedByUser()
    {
        return $this->belongsTo(User::class, 'requested_by_user');
    }

    public function hodApprovedBy()
    {
        return $this->belongsTo(User::class, 'hod_approved_by');
    }

    public function principalApprovedBy()
    {
        return $this->belongsTo(User::class, 'principal_approved_by');
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
