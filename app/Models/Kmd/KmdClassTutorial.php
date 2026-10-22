<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KmdClassTutorial extends Model
{
    protected $connection = 'mysql';
    protected $table = 'kmd_class_tutorial';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'rayon_id',
        'pengajar_id',
        'class_id',
        'modul_id',
        'video_id', // kom_v02: titik tutorial (NULL = akhir modul)
        'category_diklat',
        'quota',
        'jml_ikut',
        'sisa_quota',    
        'description',
        'status',
        'status_diklat',
        'notifikasi',
        'zoom_meeting_id',
        'topic',
        'start_time',
        'duration',
        'host_email',
        'start_url',
        'join_url',
        'no_whatsapp',
        'email_cabang',
        'notification_before',  
        'notif_sent_at',
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

    public function rayon()
    {
        return $this->belongsTo(Cabang::class, 'rayon_id');
    }

    public function user()
    {
        return $this->belongsTo(KomUser ::class, 'user_id');
    }

    // Relasi peserta dengan eager loading user
    public function peserta()
    {
        return $this->hasMany(KmdUserClassTutorial::class, 'class_tutorial_id')->with('user');
    }

    public function pengajar()
    {
        return $this->belongsTo(KomUser ::class, 'pengajar_id');
    }

    public function kelas()
    {
        return $this->belongsTo(KmdClass::class, 'class_id');
    }

    public function modul()
    {
        return $this->belongsTo(KmdModuls::class, 'modul_id');
    }

    /** kom_v02: video titik tutorial (NULL = akhir modul) */
    public function titikVideo()
    {
        return $this->belongsTo(KmdVideo::class, 'video_id');
    }

    /** kom_v02: label titik, mis. "Setelah Video 4" / "Akhir Modul" */
    public function getLabelTitikAttribute(): string
    {
        return \App\Services\Kmd\TitikTutorialService::label($this->titikVideo);
    }

    public function donations()
    {
        return $this->hasMany(KmdDonasi::class, 'class_id');
    }

    public function absensis()
    {
        return $this->hasMany(KmdUserAbsensi::class, 'userclasstutorial_id', 'id');
    }

    public function pertanyaan()
    {
        return $this->hasMany(KmdPertanyaanUser ::class, 'kelas_tutorial_id', 'id')->with('user');
    }

    public function classTutorial()
    {
        return $this->belongsTo(KmdClassTutorial::class, 'class_tutorial_id');
    }
}
