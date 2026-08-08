<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    public $timestamps = false;
    protected $fillable = ['po_id', 'item_id', 'quantity', 'unit_price', 'received_qty'];

    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class, 'item_id'); }

    public function getTotalPriceAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }
}
