<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunicationLog extends Model
{
    protected $fillable = ['type', 'subject', 'body', 'audience_meta', 'sent_count', 'failed_count', 'status', 'sent_by', 'sent_at'];
    protected $casts = ['audience_meta' => 'array', 'sent_at' => 'datetime'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
