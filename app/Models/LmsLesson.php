<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsLesson extends Model
{
    protected $fillable = ['unit_id', 'title', 'type', 'body', 'video_url', 'file_path', 'order', 'is_published'];
    protected $casts = ['is_published' => 'boolean'];

    public function unit()
    {
        return $this->belongsTo(LmsUnit::class, 'unit_id');
    }

    public function progress()
    {
        return $this->hasMany(LmsLessonProgress::class, 'lesson_id');
    }

    public function isCompletedBy(int $studentId): bool
    {
        return $this->progress()->where('student_id', $studentId)->exists();
    }

    public function youtubeEmbedUrl(): ?string
    {
        if (!$this->video_url) return null;
        $patterns = [
            '/youtu\.be\/([^?]+)/',
            '/youtube\.com\/watch\?v=([^&]+)/',
            '/youtube\.com\/embed\/([^?]+)/',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $this->video_url, $m)) {
                return 'https://www.youtube.com/embed/' . $m[1];
            }
        }
        return $this->video_url;
    }
}
