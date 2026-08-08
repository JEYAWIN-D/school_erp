<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockIssuanceItem extends Model
{
    public $timestamps = false;
    protected $fillable = ['issuance_id', 'item_id', 'quantity', 'remark'];

    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class, 'item_id'); }
}
