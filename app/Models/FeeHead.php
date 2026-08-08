<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeHead extends Model
{
    protected $fillable = [
        'name', 'code', 'description', 'is_active', 'sort_order',
        'gst_applicable', 'gst_percent', 'hsn_code', 'gst_type',
        'tally_ledger_name',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'gst_applicable' => 'boolean',
        'gst_percent'    => 'decimal:2',
    ];

    public function feeStructures(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FeeStructure::class);
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FeePayment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
