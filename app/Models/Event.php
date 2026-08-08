<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'name', 'event_type', 'event_date', 'start_time', 'end_time',
        'venue', 'description', 'banner_image', 'is_published', 'allow_rsvp',
        'max_rsvp', 'audience', 'created_by',
    ];

    protected $casts = [
        'event_date'  => 'date',
        'is_published' => 'boolean',
        'allow_rsvp'  => 'boolean',
    ];

    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function rsvps(): HasMany { return $this->hasMany(EventRsvp::class); }
    public function photos(): HasMany { return $this->hasMany(EventPhoto::class); }

    public function scopePublished($q) { return $q->where('is_published', true); }
    public function scopeUpcoming($q) { return $q->where('event_date', '>=', today()); }
}
