<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'po_number', 'vendor_id', 'requisition_id', 'order_date', 'expected_delivery',
        'total_amount', 'status', 'terms', 'created_by',
    ];

    protected $casts = ['order_date' => 'date', 'expected_delivery' => 'date', 'total_amount' => 'float'];

    public function vendor(): BelongsTo    { return $this->belongsTo(Vendor::class); }
    public function requisition(): BelongsTo { return $this->belongsTo(PurchaseRequisition::class); }
    public function createdBy(): BelongsTo  { return $this->belongsTo(User::class, 'created_by'); }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class, 'po_id');
    }

    public function grnRecords(): HasMany
    {
        return $this->hasMany(GrnRecord::class, 'po_id');
    }
}
