<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AcademicYear extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date', 'is_current', 'is_locked'];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_current' => 'boolean',
        'is_locked'  => 'boolean',
    ];

    private static ?self $memoizedCurrent = null;

    public static function current(): ?self
    {
        if (self::$memoizedCurrent !== null) {
            return self::$memoizedCurrent;
        }

        self::$memoizedCurrent = Cache::remember('current_academic_year', 3600, function () {
            return static::where('is_current', true)->first();
        });

        return self::$memoizedCurrent;
    }

    public static function clearCurrentCache(): void
    {
        self::$memoizedCurrent = null;
        Cache::forget('current_academic_year');
    }

    protected static function booted(): void
    {
        static::saved(function () {
            static::clearCurrentCache();
        });

        static::deleted(function () {
            static::clearCurrentCache();
        });
    }
}

