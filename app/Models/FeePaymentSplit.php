<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeePaymentSplit extends Model
{
    protected $fillable = [
        'fee_payment_id',
        'payment_mode',
        'amount',
        'transaction_id',
        'cheque_number',
        'cheque_date',
        'bank_name',
        'branch_name',
        'payment_account',
        'remarks',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'cheque_date' => 'date',
    ];

    public function feePayment(): BelongsTo
    {
        return $this->belongsTo(FeePayment::class);
    }
}
