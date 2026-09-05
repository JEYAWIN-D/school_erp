<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionInventoryIssue extends Model
{
    protected $fillable = [
        'enquiry_id',
        'student_id',
        'academic_year_id',
        'item_id',
        'default_quantity',
        'additional_quantity',
        'total_quantity',
        'unit_charge',
        'additional_charge',
        'inventory_transaction_id',
    ];

    protected $casts = [
        'default_quantity'    => 'integer',
        'additional_quantity' => 'integer',
        'total_quantity'      => 'integer',
        'unit_charge'         => 'float',
        'additional_charge'   => 'float',
    ];

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class, 'enquiry_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(InventoryTransaction::class, 'inventory_transaction_id');
    }
}
