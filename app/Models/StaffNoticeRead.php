<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffNoticeRead extends Model
{
    public $timestamps = false;
    protected $fillable = ['staff_notice_id', 'user_id', 'read_at'];
    protected $casts = ['read_at' => 'datetime'];
}
