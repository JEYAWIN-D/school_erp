<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Classes extends Model
{
    protected $table = 'classes';

    protected $fillable = ['name', 'numeric_value', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function sections(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Section::class, 'class_id');
    }

    public function enrollments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentEnrollment::class, 'class_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function getNameAttribute($value)
    {
        if ($value !== null && is_numeric($value)) {
            return static::toRoman((int)$value);
        }
        return $value;
    }

    public static function toRoman($num): string
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V',
            6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X',
            11 => 'XI', 12 => 'XII'
        ];
        return $map[(int)$num] ?? (string)$num;
    }

    public static function activeCached()
    {
        return Cache::remember('active_classes_list', 3600, function () {
            return static::active()->get();
        });
    }

    protected static function booted(): void
    {
        static::saved(fn() => Cache::forget('active_classes_list'));
        static::deleted(fn() => Cache::forget('active_classes_list'));
    }
}

