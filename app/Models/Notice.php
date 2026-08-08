<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = [
        'title', 'content', 'notice_type', 'target_audience',
        'target_class_id', 'publish_date', 'expiry_date',
        'attachment', 'is_published', 'created_by',
    ];

    protected $casts = [
        'publish_date' => 'date',
        'expiry_date'  => 'date',
        'is_published' => 'boolean',
    ];

    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function targetClass() { return $this->belongsTo(Classes::class, 'target_class_id'); }
    public function reads() { return $this->hasMany(NoticeRead::class); }

    public function scopeActive($q)
    {
        return $q->where('is_published', true)
                 ->where('publish_date', '<=', today())
                 ->where(fn($q) => $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', today()));
    }
}
