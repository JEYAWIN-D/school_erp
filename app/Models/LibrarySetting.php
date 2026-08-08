<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibrarySetting extends Model
{
    protected $fillable = ['member_type', 'fine_per_day', 'loan_days', 'max_books', 'max_renewals'];

    public static function forType(string $type = 'student'): self
    {
        return static::firstOrCreate(['member_type' => $type], [
            'fine_per_day' => 1.00,
            'loan_days'    => 14,
            'max_books'    => 2,
            'max_renewals' => 1,
        ]);
    }
}
