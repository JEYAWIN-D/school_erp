<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class GradingSchemeRange extends Model {
    protected $fillable = ['scheme_id','min_marks','max_marks','grade','description','gpa_points'];
    public function scheme() { return $this->belongsTo(GradingScheme::class,'scheme_id'); }
}
