<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Designation extends Model {
    protected $fillable = ['department_id','name','grade','spatie_role','pay_band_min','pay_band_max','pay_scale','description','is_active'];
    protected $casts = ['is_active'=>'boolean','pay_band_min'=>'float','pay_band_max'=>'float'];
    public function department() { return $this->belongsTo(Department::class); }
    public function employees() { return $this->hasMany(Employee::class); }
}
