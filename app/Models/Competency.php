<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competency extends Model
{
    protected $fillable = [
        'subject_id', 'class_id', 'name', 'code', 'description',
        'domain', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function class(): BelongsTo  { return $this->belongsTo(Classes::class, 'class_id'); }
    public function assessments(): HasMany { return $this->hasMany(CompetencyAssessment::class); }
}
