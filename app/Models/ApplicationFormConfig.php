<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApplicationFormConfig extends Model {
    protected $fillable = ['academic_year_id','class_id','title','description','fields','document_fields',
        'application_fee','open_from','open_until','link_token','is_active'];
    protected $casts = ['fields'=>'array','document_fields'=>'array','is_active'=>'boolean',
        'open_from'=>'date','open_until'=>'date'];

    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function class() { return $this->belongsTo(Classes::class, 'class_id'); }
    public function applications() { return $this->hasMany(StudentApplication::class, 'form_config_id'); }

    protected static function booted(): void {
        static::creating(function ($m) {
            if (!$m->link_token) $m->link_token = Str::random(32);
        });
    }
}
