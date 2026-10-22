<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'video_id',       // kom_v02: titik tutorial (NULL = akhir modul)
        'is_rebook',      // kom_v02: jadwal pengganti (tidak hadir) tanpa persembahan lagi
        'rebook_from_id', // kom_v02
        'category_diklat',
        'class_id',
        'rayon_id',
        'date_booking',
        'status',
        'zoom_registrant_id',
        'zoom_join_url',
        'step',
        'pengajar_id',
        'status_progress',
        'checkout_expires_at', // kom_v02: batas waktu memberikan persembahan
    ];

    protected $casts = [
        'checkout_expires_at' => 'datetime', // kom_v02
        'is_rebook' => 'boolean',
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
    
    /** kom_v02 */
    public function titikVideo()
    {
        return $this->belongsTo(KmdVideo::class, 'video_id');
    }

    public function absensi()
    {
        return $this->hasOne(KmdUserAbsensi::class, 'userclasstutorial_id', 'id');
    }

    /**
     * kom_v02: hanya pendaftaran yang SUDAH LUNAS (rangkaian selesai).
     * Termasuk jadwal pengganti (is_rebook) yang tidak membayar lagi, dan data lama
     * yang lunasnya hanya tercatat di tabel persembahan (kmd_donations).
     */
    public function scopeLunas($query)
    {
        $tabel = $this->getTable();

        return $query->where(function ($q) use ($tabel) {
            $q->where($tabel . '.status_progress', 'paid')
              ->orWhereExists(function ($d) use ($tabel) {
                  $d->selectRaw('1')->from('kmd_donations')
                    ->whereColumn('kmd_donations.userclasstutorial_id', $tabel . '.id')
                    ->where('kmd_donations.status', 'paid');
              });
        });
    }

}
