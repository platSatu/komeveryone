<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KmdZoomMeetings extends Model
{
    protected $connection = 'mysql';
    protected $table = 'kmd_zoom_meetings';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'zoom_meeting_id',
        'topic',
        'start_time',
        'duration',
        'host_email',
        'start_url',
        'join_url',
        'status',
        'user_id',
        'pengajar_id',
        'cabang_id',
        'kelas_id',
        'modul_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $casts = [
        'start_time' => 'datetime',
    ];

    // Relasi ke user pembuat meeting
    public function user()
    {
        return $this->belongsTo(KomUser::class, 'user_id');
    }

    // Relasi ke pengajar
    public function pengajar()
    {
        return $this->belongsTo(KomUser::class, 'pengajar_id');
    }

    // Relasi ke cabang / rayon
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    // Relasi ke kelas
    public function kelas()
    {
        return $this->belongsTo(KmdClass::class, 'kelas_id');
    }

    // Relasi ke modul
    public function modul()
    {
        return $this->belongsTo(KmdModuls::class, 'modul_id');
    }
}
