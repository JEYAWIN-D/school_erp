<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title', 'author', 'publisher', 'isbn', 'edition', 'year',
        'category', 'subject_id', 'language', 'pages',
        'total_copies', 'available_copies', 'lost_copies',
        'purchase_price', 'purchase_date', 'location', 'description',
        'cover_image', 'is_active',
        'is_deaccessioned', 'deaccession_date', 'deaccession_reason', 'deaccession_by',
    ];

    protected $casts = [
        'purchase_date'     => 'date',
        'is_active'         => 'boolean',
        'is_deaccessioned'  => 'boolean',
        'deaccession_date'  => 'date',
    ];

    public function issues(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BookIssue::class);
    }

    public function reservations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BookReservation::class);
    }

    public function subject(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
