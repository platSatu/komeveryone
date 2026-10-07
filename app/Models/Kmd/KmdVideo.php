<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KmdVideo extends Model
{
    protected $connection = 'mysql';
    protected $table = 'kmd_videos';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'description',
        'durations',
        'sort_video',
        'url_video',
        'video',
        'modul_id',
        'status',
        'source_video_id', // kom_v02
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // ✅ Existing relationships (tidak diubah)
    public function modul()
    {
        return $this->belongsTo(KmdModuls::class, 'modul_id');
    }

    public function progress()
    {
        return $this->hasMany(KmdUserVideoProgress::class, 'video_id');
    }

    // 🆕 NEW: Relationship ke questions (yang dibutuhkan)
    public function questions()
    {
        return $this->hasMany(KmdQuestionVideos::class, 'videos_id', 'id');
    }

    // 🆕 NEW: Helper method untuk cek apakah video punya pertanyaan
    public function hasQuestions()
    {
        return $this->questions()->exists();
    }

    // 🆕 NEW: Get pertanyaan pertama (untuk kemudahan)
    public function getFirstQuestion()
    {
        return $this->questions()->with('question.answers')->first();
    }

    // 🆕 NEW: Cek apakah user sudah menjawab pertanyaan video ini
    public function isAnsweredByUser($userId)
    {
        return $this->progress()
            ->where('user_id', $userId)
            ->whereNotNull('answered_question')
            ->where('is_correct', true)
            ->exists();
    }

    // 🆕 NEW: Get progress user untuk video ini
    public function getUserProgress($userId)
    {
        return $this->progress()
            ->where('user_id', $userId)
            ->first();
    }

    // 🆕 NEW: Cek apakah video sudah completed oleh user
    public function isCompletedByUser($userId)
    {
        return $this->progress()
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->exists();
    }
}
