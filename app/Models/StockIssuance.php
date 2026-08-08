<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockIssuance extends Model
{
    protected $fillable = ['issue_number', 'issue_date', 'issued_to', 'issued_by', 'purpose'];
    protected $casts = ['issue_date' => 'date'];

    public function issuedBy(): BelongsTo { return $this->belongsTo(User::class, 'issued_by'); }

    public function items(): HasMany
    {
        return $this->hasMany(StockIssuanceItem::class, 'issuance_id');
    }
}
