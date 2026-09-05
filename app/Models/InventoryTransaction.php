<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'transaction_code',
        'item_id',
        'transaction_type', // stock_in, manual_stock_out, admission_issue, adjustment_increase, adjustment_decrease, return_reversal
        'quantity',
        'previous_stock',
        'quantity_changed',
        'new_stock',
        'unit_cost',
        'total_cost',
        'reference_type',
        'reference_id',
        'invoice_number',
        'invoice_date',
        'supplier_name',
        'invoice_path',
        'notes',
        'performed_by',
    ];

    protected $casts = [
        'quantity'         => 'integer',
        'previous_stock'   => 'integer',
        'quantity_changed' => 'integer',
        'new_stock'        => 'integer',
        'unit_cost'        => 'float',
        'total_cost'       => 'float',
        'invoice_date'     => 'date',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function getInvoiceUrlAttribute(): ?string
    {
        if (!$this->invoice_path) return null;
        return Storage::url($this->invoice_path);
    }

    public static function generateTransactionCode(string $prefix = 'TXN'): string
    {
        $dateStr = now()->format('Ymd');
        $count = self::whereDate('created_at', today())->count() + 1;
        return sprintf('%s-%s-%04d', $prefix, $dateStr, $count);
    }
}
