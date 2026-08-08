<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LateFeeRule extends Model {
    protected $fillable = ['fee_head_id','rule_type','amount','grace_days','is_active'];
    protected $casts = ['is_active'=>'boolean'];
    public function feeHead() { return $this->belongsTo(FeeHead::class); }
    public function calculate(int $daysLate): float {
        $effectiveDays = max(0, $daysLate - $this->grace_days);
        return $this->rule_type === 'per_day' ? $this->amount * $effectiveDays : ($effectiveDays > 0 ? $this->amount : 0);
    }
}
