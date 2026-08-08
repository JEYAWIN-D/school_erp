<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GrnRecord extends Model
{
    protected $table = 'grn_records';
    protected $fillable = [
        'grn_number', 'po_id', 'received_date', 'invoice_number',
        'invoice_date', 'invoice_amount', 'remarks', 'received_by',
    ];

    protected $casts = ['received_date' => 'date', 'invoice_date' => 'date'];

    public function purchaseOrder(): BelongsTo { return $this->belongsTo(PurchaseOrder::class, 'po_id'); }
    public function receivedBy(): BelongsTo     { return $this->belongsTo(User::class, 'received_by'); }

    public function items(): HasMany
    {
        return $this->hasMany(GrnItem::class, 'grn_id');
    }
}
