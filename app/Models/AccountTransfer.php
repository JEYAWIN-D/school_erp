<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountTransfer extends Model
{
    protected $fillable = [
        'transfer_number',
        'from_account',
        'to_account',
        'amount',
        'transfer_date',
        'reference_no',
        'remarks',
        'transferred_by',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'amount'        => 'decimal:2',
    ];

    public function transferredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }

    public static function generateNumber(): string
    {
        return 'TRF-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
    }

    public function getFormattedAmountAttribute(): string
    {
        return '₹' . number_format($this->amount, 2);
    }
}
