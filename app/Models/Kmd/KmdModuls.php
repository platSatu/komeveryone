<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class KmdModuls extends Model
{
    protected $connection = 'mysql';
    protected $table = 'kmd_moduls';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'description',
        'order',
        'kom_class_id',
        'status',
        'language_code', // kom_v02
        'source_modul_id', // kom_v02
    ];

    // Auto-generate UUID saat membuat record baru
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            Log::info('Koneksi saat create:', [
            'connection' => $model->getConnectionName()
            ]);

            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function kelas()
    {
        return $this->belongsTo(KmdClass::class, 'kom_class_id');
    }

    public function videos()
    {
        return $this->hasMany(KmdVideo::class, 'modul_id', 'id');
    }

    // ✅ Relationship ke progress modul
    public function progress()
    {
        return $this->hasMany(KmdProgressModul::class, 'moduls_id', 'id');
    }

    // ✅ Helper methods
    public function isCompletedByUser($userId)
    {
        return $this->progress()
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->exists();
    }

    public function getUserProgress($userId)
    {
        return $this->progress()
            ->where('user_id', $userId)
            ->first();
    }

    public function getCompletionPercentage($userId)
    {
        $totalVideos = $this->videos()->count();
        if ($totalVideos == 0) return 0;

        // 🔧 Gunakan relationship untuk lebih efisien
        $completedVideos = \App\Models\Kmd\KmdUserVideoProgress::where('user_id', $userId)
            ->whereIn('video_id', $this->videos()->pluck('id'))
            ->where('status', 'completed')
            ->count();

        return round(($completedVideos / $totalVideos) * 100);
    }
}
