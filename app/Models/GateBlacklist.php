<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GateBlacklist extends Model
{
    protected $table = 'gate_blacklist';
    protected $fillable = ['name', 'phone', 'id_number', 'reason', 'is_active', 'added_by'];
    protected $casts = ['is_active' => 'boolean'];

    public function addedBy(): BelongsTo { return $this->belongsTo(User::class, 'added_by'); }

    public static function isBlacklisted(string $phone = null, string $idNumber = null): bool
    {
        return self::where('is_active', true)
            ->where(function ($q) use ($phone, $idNumber) {
                if ($phone)    $q->orWhere('phone', $phone);
                if ($idNumber) $q->orWhere('id_number', $idNumber);
            })->exists();
    }
}
