<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;


class KomUser extends Authenticatable
{
    //use HasApiTokens, Notifiable;
    use HasApiTokens;

    // Gunakan nama tabel eksplisit
    protected $connection = 'mysql';
    protected $table = 'kmd_users';

    // UUID sebagai primary key
    protected $keyType = 'string';
    public $incrementing = false;

    // Fields yang boleh diisi mass-assignment
    protected $fillable = [
        'id',
        'nik',
        'name',
        'email',
        'password',
        'role',
        'cabang_id',
        'phone',
        'asal_gereja',
        'tahun_lahir',
        'image',
        'status',
        'idnkp',
        'statactive',
        'jenis',
        'no_sim',
        'email_verification_token',
        'email_verification_sent_at',
        'email_verified_at',
        'remember_token',
        'language_code', // kom_v02
    ];

    // public function isSuperadmin()
    // {
    //     return $this->role === 'superadmin'; // Ubah sesuai struktur database kamu
    // }

    // Otomatis set UUID saat membuat data baru
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // Jika kamu mau hidden beberapa field (opsional)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    // Tambahkan relasi ini di KomUser.php
    public function pengajarReviews()
    {
        return $this->hasMany(KmdUserReview::class, 'pengajar_id');
    }

    public function userReviews()
    {
        return $this->hasMany(KmdUserReview::class, 'user_id');
    }

    public function rekening()
    {
        return $this->hasOne(KmdUserRekening::class, 'user_id');
    }

    public function progressVideos()
    {
        return $this->hasMany(KmdUserVideoProgress::class, 'user_id');
    }

    public function tutorialStatuses()
    {
        return $this->hasMany(KmdUserTutorialStatus::class, 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(KmdUserReview::class, 'user_id');
    }

    public function questions()
    {
        return $this->hasMany(KmdUserQuestions::class, 'user_id');
    }

    public function modules()
    {
        return $this->hasMany(KmdUserModul::class, 'user_id');
    }

    public function progressModules()
    {
        return $this->hasMany(KmdProgressModul::class, 'user_id');
    }

    public function classTutorials()
    {
        return $this->hasMany(KmdUserClassTutorial::class, 'user_id');
    }

    public function absensis()
    {
        return $this->hasMany(KmdUserAbsensi::class, 'user_id');
    }

    public function progressModuls()
    {
        return $this->hasMany(KmdProgressModul::class, 'user_id');
    }

    public function books()
    {
        return $this->hasMany(KmdBooks::class, 'user_id');
    }

    public function pertanyaanUsers()
    {
        return $this->hasMany(KmdPertanyaanUser::class, 'user_id');
    }

    // Contoh model User (misalnya KomUser)
    public function donasiPaid()
    {
        return $this->hasMany(KmdDonasi::class, 'user_id', 'id')->where('status', 'paid');
    }

    public function kmdUserClassTutorial(): HasMany
    {
        return $this->hasMany(KmdUserClassTutorial::class, 'user_id');
    }

    // ===================== kom_v02 =====================

    public function userClasses()
    {
        return $this->hasMany(KmdUserClass::class, 'user_id');
    }

    /** Rangkaian kelas yang sedang berjalan (bahasa terkunci selama ada). */
    public function activeUserClass()
    {
        return $this->hasOne(KmdUserClass::class, 'user_id')
            ->where('status', KmdUserClass::STATUS_ACTIVE)
            ->latestOfMany('enrolled_at');
    }

    /** User wajib memilih bahasa dulu (user baru). */
    public function needsLanguageSelection(): bool
    {
        return empty($this->language_code);
    }

    /** Bahasa boleh diganti hanya jika tidak ada rangkaian kelas yang sedang berjalan. */
    public function canChangeLanguage(): bool
    {
        return ! $this->userClasses()->where('status', KmdUserClass::STATUS_ACTIVE)->exists();
    }
}
