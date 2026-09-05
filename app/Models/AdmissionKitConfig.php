<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionKitConfig extends Model
{
    protected $fillable = [
        'class_id',
        'item_id',
        'academic_year_id',
        'default_quantity',
        'unit',
        'is_default_included',
        'allow_additional_qty',
        'student_charge',
    ];

    protected $casts = [
        'default_quantity'     => 'integer',
        'is_default_included'  => 'boolean',
        'allow_additional_qty' => 'boolean',
        'student_charge'       => 'float',
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}
