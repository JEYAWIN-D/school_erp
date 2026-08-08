<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class GradingScheme extends Model {
    protected $fillable = ['name','scheme_type','is_default','is_active'];
    protected $casts = ['is_default'=>'boolean','is_active'=>'boolean'];
    public function ranges() { return $this->hasMany(GradingSchemeRange::class,'scheme_id'); }
    public function gradeFor(float $marks): string {
        $range = $this->ranges()->where('min_marks','<=',$marks)->where('max_marks','>=',$marks)->first();
        return $range?->grade ?? 'F';
    }
}
