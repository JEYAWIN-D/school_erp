<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnquiryFollowUp extends Model
{
    protected $fillable = ['enquiry_id', 'notes', 'next_follow_up_date', 'status', 'created_by'];

    protected $casts = ['next_follow_up_date' => 'date'];

    public function enquiry(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function createdBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
