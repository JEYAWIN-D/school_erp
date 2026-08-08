<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alumni extends Model
{
    protected $table = 'alumni';
    protected $fillable = [
        'student_id', 'first_name', 'last_name', 'email', 'phone',
        'passing_year', 'last_class', 'current_occupation', 'current_employer',
        'current_city', 'achievements', 'profile_photo', 'linkedin_url',
        'is_verified', 'is_active',
    ];

    protected $casts = ['is_verified' => 'boolean', 'is_active' => 'boolean'];

    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function scopeActive($q) { return $q->where('is_active', true); }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
