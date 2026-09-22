<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffEvent extends Model
{
    protected $table = 'staff_events';

    protected $fillable = [
        'employee_id',
        'event_type',
        'title',
        'event_date',
        'description',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', today()->toDateString())
            ->orderBy('event_date', 'asc');
    }

    public function getEventBadgeColorAttribute(): string
    {
        return match(strtolower($this->event_type)) {
            'birthday'             => 'bg-pink-100 text-pink-700 border-pink-200',
            'wedding'              => 'bg-purple-100 text-purple-700 border-purple-200',
            'wedding_anniversary'  => 'bg-rose-100 text-rose-700 border-rose-200',
            'joining_anniversary'  => 'bg-indigo-100 text-indigo-700 border-indigo-200',
            'retirement'           => 'bg-amber-100 text-amber-700 border-amber-200',
            default                => 'bg-blue-100 text-blue-700 border-blue-200',
        };
    }

    public function getBadgeColorAttribute(): string
    {
        return $this->event_badge_color;
    }

    public function getEventLabelAttribute(): string
    {
        return match(strtolower($this->event_type)) {
            'birthday'             => 'Birthday',
            'wedding'              => 'Wedding',
            'wedding_anniversary'  => 'Wedding Anniversary',
            'joining_anniversary'  => 'Joining Anniversary',
            'retirement'           => 'Retirement',
            default                => ucfirst(str_replace('_', ' ', $this->event_type ?? 'Event')),
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->event_label;
    }
}
