<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffNotice extends Model
{
    protected $fillable = ['title', 'body', 'priority', 'target_department', 'created_by', 'expires_at'];
    protected $casts = ['expires_at' => 'datetime'];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reads()
    {
        return $this->hasMany(StaffNoticeRead::class, 'staff_notice_id');
    }

    public function isReadBy(int $userId): bool
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }
}
