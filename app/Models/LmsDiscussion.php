<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsDiscussion extends Model
{
    protected $fillable = ['course_id', 'user_id', 'title', 'body', 'is_answered', 'is_hidden'];
    protected $casts = ['is_answered' => 'boolean', 'is_hidden' => 'boolean'];

    public function course()
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(LmsDiscussionReply::class, 'discussion_id')->where('is_hidden', false)->latest();
    }
}
