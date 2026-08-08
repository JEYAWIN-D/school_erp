<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class QuestionBank extends Model {
    protected $table = 'question_bank';
    protected $fillable = ['subject_id','class_id','question_type','question','option_a','option_b','option_c','option_d','correct_answer','marks','difficulty','chapter','is_active'];
    protected $casts = ['is_active'=>'boolean','marks'=>'float'];
    public function subject() { return $this->belongsTo(Subject::class); }
    public function class() { return $this->belongsTo(Classes::class,'class_id'); }
}
