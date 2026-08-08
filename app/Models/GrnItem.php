<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GrnItem extends Model
{
    public $timestamps = false;
    protected $table = 'grn_items';
    protected $fillable = [
        'grn_id', 'item_id', 'po_item_id', 'received_qty', 'accepted_qty', 'rejected_qty', 'rejection_reason',
    ];

    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class, 'item_id'); }
}
