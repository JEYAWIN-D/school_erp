<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryItem extends Model
{
    protected $fillable = [
        'category_id', 'name', 'item_code', 'unit', 'unit_price',
        'reorder_level', 'current_stock', 'location', 'description', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean', 'unit_price' => 'float'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function scopeActive($q) { return $q->where('is_active', true); }

    public function isLowStock(): bool
    {
        return $this->reorder_level > 0 && $this->current_stock <= $this->reorder_level;
    }
}
