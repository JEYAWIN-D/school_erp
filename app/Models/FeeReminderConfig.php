<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeReminderConfig extends Model
{
    protected $fillable = [
        'name', 'before_due_days', 'on_due_date', 'after_due_days',
        'channel', 'email_subject', 'email_body', 'is_active', 'updated_by',
    ];

    protected $casts = [
        'before_due_days' => 'array',
        'after_due_days'  => 'array',
        'on_due_date'     => 'boolean',
        'is_active'       => 'boolean',
    ];

    public function getDefaultSubjectAttribute(): string
    {
        return $this->email_subject ?: 'Fee Payment Reminder – {{school_name}}';
    }

    public function getDefaultBodyAttribute(): string
    {
        return $this->email_body ?: "Dear {{parent_name}},\n\nThis is a reminder that a fee payment of ₹{{balance}} is due for {{student_name}} ({{class}}) on {{due_date}}.\n\nPlease clear the dues at the earliest to avoid any inconvenience.\n\nRegards,\n{{school_name}}";
    }
}
