<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequisitionItem extends Model
{
    public $timestamps = false;
    protected $fillable = ['requisition_id', 'item_id', 'quantity', 'estimated_price', 'remark'];

    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class, 'item_id'); }
}
