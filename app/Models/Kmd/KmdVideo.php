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
        'has_tutorial',    // kom_v02: ada kelas tutorial setelah video ini (tengah modul)
    ];

    protected $casts = [
        'has_tutorial' => 'boolean',
    ];

    /**
     * kom_v02: durasi video dalam DETIK.
     * Kolom `durations` diisi dengan format "jam:menit:detik" (mis. 00:18:46); dulu dibaca
     * dengan (int) sehingga selalu 0. Mendukung "hh:mm:ss", "mm:ss", dan angka detik.
     */
    public static function durasiKeDetik($nilai): int
    {
        $nilai = trim((string) $nilai);
        if ($nilai === '') {
            return 0;
        }

        if (str_contains($nilai, ':')) {
            $bagian = array_map('intval', explode(':', $nilai));
            $detik = 0;
            foreach ($bagian as $b) {
                $detik = $detik * 60 + max(0, $b);
            }

            return $detik;
        }

        return max(0, (int) $nilai);
    }

    public function getDurasiDetikAttribute(): int
    {
        return self::durasiKeDetik($this->durations);
    }

    /** Durasi untuk ditampilkan: "18:46" atau "1:05:10" */
    public function getDurasiTampilAttribute(): string
    {
        $d = $this->durasi_detik;

        return $d >= 3600 ? gmdate('G:i:s', $d) : gmdate('i:s', $d);
    }

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
