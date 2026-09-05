<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $fillable = [
        'category_id',
        'inventory_type',     // academic vs general_operations
        'academic_category',  // uniform, shoes, socks, school_bag, notebooks, textbooks, stationery, other_academic
        'name',
        'item_code',
        'unit',
        'unit_price',         // legacy / purchase cost fallback
        'purchase_cost',      // Unit Purchase Cost
        'student_price',      // Selling / Student Charge
        'reorder_level',      // Minimum Stock Level
        'current_stock',
        'location',
        'supplier_name',      // Default Supplier / Vendor Name
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'unit_price'     => 'float',
        'purchase_cost'  => 'float',
        'student_price'   => 'float',
        'current_stock'  => 'integer',
        'reorder_level'  => 'integer',
    ];

    protected $appends = [
        'effective_purchase_cost',
        'effective_student_price',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'item_id')->latest();
    }

    public function kitConfigs(): HasMany
    {
        return $this->hasMany(AdmissionKitConfig::class, 'item_id');
    }

    public function admissionIssues(): HasMany
    {
        return $this->hasMany(AdmissionInventoryIssue::class, 'item_id');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeAcademic($q)
    {
        return $q->where('inventory_type', 'academic');
    }

    public function scopeGeneralOperations($q)
    {
        return $q->where('inventory_type', 'general_operations');
    }

    public function scopeLowStock($q)
    {
        return $q->where('reorder_level', '>', 0)
                 ->where('current_stock', '>', 0)
                 ->whereColumn('current_stock', '<=', 'reorder_level');
    }

    public function scopeOutOfStock($q)
    {
        return $q->where('current_stock', '<=', 0);
    }

    public function getEffectivePurchaseCostAttribute(): float
    {
        return (float) ($this->purchase_cost > 0 ? $this->purchase_cost : $this->unit_price);
    }

    public function getEffectiveStudentPriceAttribute(): float
    {
        return (float) ($this->student_price > 0 ? $this->student_price : $this->unit_price);
    }

    public function isLowStock(): bool
    {
        return $this->reorder_level > 0 && $this->current_stock <= $this->reorder_level && $this->current_stock > 0;
    }

    public function isOutOfStock(): bool
    {
        return $this->current_stock <= 0;
    }
}
