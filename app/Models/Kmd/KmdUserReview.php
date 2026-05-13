<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KmdUserReview extends Model
{
    protected $connection = 'mysql';
    protected $table = 'kmd_user_review';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'pengajar_id',
        'userclasstutorial_id',
        'isi_pengajaran',
        'kebenaran_teologis',
        'pengajar_sampaikan_seluruh_materi',
        'pengajar_sampaikan_materi_utama',
        'pembukaan_materi',
        'kesinambungan_materi',
        'kesaksian_pribadi',
        'materi_dipahami',
        'sesuai_dengan_materi',
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

    // Relasi ke user peserta
    public function user()
    {
        return $this->belongsTo(KomUser::class, 'user_id');
    }

    // Relasi ke pengajar
    public function pengajar()
    {
        return $this->belongsTo(KomUser::class, 'pengajar_id');
    }

    // Relasi ke user_class_tutorial
    public function userClassTutorial()
    {
        return $this->belongsTo(KmdUserClassTutorial::class, 'userclasstutorial_id');
    }

    public function classTutorial()
    {
        return $this->belongsTo(KmdClassTutorial::class, 'userclasstutorial_id');
    }
}
