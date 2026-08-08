<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FeeInstallmentPlan extends Model {
    protected $fillable = [
        'name','class_id','academic_year_id','installments_count',
        'frequency','start_date','late_fee_rule_id','description','is_active',
    ];
    protected $casts = ['is_active' => 'boolean', 'start_date' => 'date'];

    public function installments() { return $this->hasMany(FeeInstallment::class, 'plan_id'); }
    public function class()        { return $this->belongsTo(Classes::class, 'class_id'); }
    public function lateFeeRule()  { return $this->belongsTo(LateFeeRule::class, 'late_fee_rule_id'); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }

    public function getFrequencyLabelAttribute(): string
    {
        return ['one_time' => 'One Time', 'monthly' => 'Monthly', 'quarterly' => 'Quarterly',
                'half_yearly' => 'Half-Yearly', 'annually' => 'Annually'][$this->frequency] ?? '—';
    }
}
