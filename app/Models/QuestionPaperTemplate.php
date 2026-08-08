<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class QuestionPaperTemplate extends Model {
    protected $fillable = [
        'name','exam_type','show_school_logo','show_school_name','show_school_address',
        'show_affiliation','header_instructions','general_instructions','watermark_text',
        'question_numbering','show_marks_per_question','show_section_totals',
        'show_answer_lines','answer_lines_count','paper_size','font_size','is_default',
    ];
    protected $casts = [
        'show_school_logo'=>'boolean','show_school_name'=>'boolean','show_school_address'=>'boolean',
        'show_affiliation'=>'boolean','show_marks_per_question'=>'boolean','show_section_totals'=>'boolean',
        'show_answer_lines'=>'boolean','is_default'=>'boolean',
    ];
    public static function getDefault(): ?static {
        return static::where('is_default', true)->first() ?? static::first();
    }
}
