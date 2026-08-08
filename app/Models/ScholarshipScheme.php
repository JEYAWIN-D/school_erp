<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ScholarshipScheme extends Model {
    protected $fillable = ['name','type','value','applicable_fee_heads','criteria_type','marks_threshold','is_active'];
    protected $casts = ['is_active'=>'boolean','applicable_fee_heads'=>'array'];
    public function concessions() { return $this->hasMany(StudentConcession::class,'scholarship_id'); }
}
