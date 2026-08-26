<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KmdUserClassTutorial extends Model
{
    protected $connection = 'mysql';
    protected $table = 'kmd_user_class_tutorial'; // pastikan ini sesuai nama tabel yang kamu pakai

    protected $keyType = 'string';  // karena id UUID
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'class_tutorial_id',
        'modul_id',
        'category_diklat',
        'class_id',
        'rayon_id',
        'date_booking',
        'status',
        'zoom_registrant_id',
        'zoom_join_url',
        'step',
        'pengajar_id',
        'status_progress'
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

    // Relasi ke user peserta
    public function user()
    {
        return $this->belongsTo(KomUser::class, 'user_id');
    }

    // Relasi ke class tutorial
    public function classTutorial()
    {
        return $this->belongsTo(KmdClassTutorial::class, 'class_tutorial_id');
    }


    // Relasi ke modul
    public function modul()
    {
        return $this->belongsTo(KmdModuls::class, 'modul_id');
    }

    // Relasi ke kelas
    public function kelas()
    {
        return $this->belongsTo(KmdClass::class, 'class_id');
    }

    // Relasi ke rayon
    public function rayon()
    {
        return $this->belongsTo(Cabang::class, 'rayon_id'); // kalau tabel rayon beda, ganti ke model yang sesuai
    }

    public function donations()
    {
        return $this->hasMany(KmdDonasi::class, 'userclasstutorial_id');
    }

    public function latestDonation()
    {
        return $this->hasOne(KmdDonasi::class, 'userclasstutorial_id')->latestOfMany();
    }

    public function pengajar(): BelongsTo
    {
        return $this->belongsTo(KomUser::class, 'pengajar_id');
    }
    
    public function absensi()
    {
        return $this->hasOne(KmdUserAbsensi::class, 'userclasstutorial_id', 'id');
    }

}
