<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsDiscussionReply extends Model
{
    protected $fillable = ['discussion_id', 'user_id', 'body', 'is_answer', 'is_hidden'];
    protected $casts = ['is_answer' => 'boolean', 'is_hidden' => 'boolean'];

    public function discussion()
    {
        return $this->belongsTo(LmsDiscussion::class, 'discussion_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
