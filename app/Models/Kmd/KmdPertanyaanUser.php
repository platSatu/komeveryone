<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class KmdPertanyaanUser extends Model
{
    use HasUuids;
    protected $connection = 'mysql';
    protected $table = 'kmd_pertanyaan_user';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'class_id',
        'modul_id',
        'video_id', // kom_v02: titik tutorial (NULL = akhir modul)
        'category_diklat',
        'kelas_tutorial_id',
        'pengajar_id',
        'rayon_id', // tambahkan ini
        'pertanyaan',
        'status',
        'edit_count',
        'tgl_submit',
        'tgl_edit',
    ];

    protected $casts = [
        'tgl_submit' => 'datetime',
        'tgl_edit' => 'datetime',
    ];

    /**
     * kom_v02: pertanyaan baru dianggap MASUK ke pengajar setelah persembahan lunas
     * (callback Midtrans). Pertanyaan dari pendaftaran yang belum lunas tidak ditampilkan.
     */
    public function scopeSudahLunas($query)
    {
        return $query->whereExists(function ($b) {
            $b->selectRaw('1')->from('kmd_user_class_tutorial as kuct_lunas')
              ->whereColumn('kuct_lunas.user_id', 'kmd_pertanyaan_user.user_id')
              ->whereColumn('kuct_lunas.class_tutorial_id', 'kmd_pertanyaan_user.kelas_tutorial_id')
              ->where(function ($q) {
                  $q->where('kuct_lunas.status_progress', 'paid')
                    ->orWhereExists(function ($d) {
                        $d->selectRaw('1')->from('kmd_donations')
                          ->whereColumn('kmd_donations.userclasstutorial_id', 'kuct_lunas.id')
                          ->where('kmd_donations.status', 'paid');
                    });
              });
        });
    }

    // Relasi ke model User (kmd_users)
    public function user()
    {
        return $this->belongsTo(KomUser::class, 'user_id');
    }

    // Relasi ke class (kmd_classes)
    public function class()
    {
        return $this->belongsTo(KmdClass::class, 'class_id');
    }

    // Relasi ke modul (kmd_moduls)
    public function modul()
    {
        return $this->belongsTo(KmdModuls::class, 'modul_id');
    }

    // Relasi ke kelas tutorial (kmd_class_tutorial)
    public function kelasTutorial()
    {
        return $this->belongsTo(KmdClassTutorial::class, 'kelas_tutorial_id');
    }

    // Relasi ke pengajar (users)
    public function pengajar()
    {
        return $this->belongsTo(KomUser::class, 'pengajar_id');
    }

    // Relasi ke rayon
    public function rayon()
    {
        return $this->belongsTo(Cabang::class, 'rayon_id');
    }
}
