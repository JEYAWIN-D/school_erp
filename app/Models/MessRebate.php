<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MessRebate extends Model {
    protected $table = 'mess_rebates';
    protected $fillable = ['allotment_id','from_date','to_date','days_absent','rebate_per_day','total_rebate','reason','status','approved_by','notes'];
    protected $casts = ['from_date'=>'date','to_date'=>'date'];
    public function allotment() { return $this->belongsTo(HostelAllotment::class,'allotment_id'); }
    public function approvedBy() { return $this->belongsTo(\App\Models\User::class,'approved_by'); }
}
