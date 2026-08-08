<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessFeedback extends Model
{
    protected $fillable = [
        'student_id', 'hostel_id', 'feedback_date', 'meal_type',
        'rating', 'comment', 'is_anonymous',
    ];

    protected $casts = [
        'feedback_date' => 'date',
        'rating'        => 'integer',
        'is_anonymous'  => 'boolean',
    ];

    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function hostel(): BelongsTo  { return $this->belongsTo(Hostel::class); }
}
