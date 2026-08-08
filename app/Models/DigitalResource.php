<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DigitalResource extends Model {
    protected $fillable = ['title','resource_type','description','file_path','external_url','class_id','subject_id','download_count','is_active','uploaded_by'];
    protected $casts = ['is_active'=>'boolean'];
    public function class() { return $this->belongsTo(Classes::class,'class_id'); }
    public function subject() { return $this->belongsTo(Subject::class); }
}
