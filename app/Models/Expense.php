<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'expense_number',
        'category',
        'subcategory',
        'title',
        'description',
        'amount',
        'expense_date',
        'payment_method',
        'vendor_name',
        'vendor_invoice_no',
        'invoice_receipt_path',
        'academic_year_id',
        'created_by',
        'approval_status',
        'verified_by',
        'verified_at',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
        'verified_at'  => 'datetime',
        'approved_at'  => 'datetime',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeAcademic($query)
    {
        return $query->where('category', 'academic');
    }

    public function scopeMaintenance($query)
    {
        return $query->where('category', 'maintenance');
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->whereIn('approval_status', ['pending', 'verified']);
    }

    // Accessors
    public function getFormattedAmountAttribute(): string
    {
        return '₹' . number_format($this->amount, 2);
    }

    public function getSubcategoryLabelAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->subcategory));
    }
}
