<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FeeInstallment extends Model {
    protected $fillable = ['plan_id','installment_number','name','due_date','amount','amount_percentage','fee_head_id'];
    protected $casts = ['due_date'=>'date'];
    public function plan() { return $this->belongsTo(FeeInstallmentPlan::class,'plan_id'); }
}
