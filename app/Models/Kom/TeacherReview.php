<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class TeacherReview extends Model
{
    protected $table = 'kom_teacher_reviews';  // ganti sesuai nama tabel migrasi kamu

    // Kolom yang bisa diisi massal (fillable)
    protected $fillable = [
        'kom_user_id',
        'kelas_tutorial_id',
        'kom_schedule_id',
        'isi_pengajaran',
        'kebenaran_teologis',
        'pengajar_sampaikan_seluruh_materi',
        'pengajar_sampaikan_materi_utama',
        'pembukaan_materi',
        'kesinambungan_materi',
        'kesaksian_pribadi',
        'materi_dipahami',
        'sesuai_dengan_materi',
        'komentar',
    ];

    // Relasi ke user yang memberikan review
    public function user()
    {
        return $this->belongsTo(KomUser::class, 'kom_user_id');
        // Ganti User::class dengan model user kamu kalau beda namespace
    }

    // Relasi ke jadwal kelas
    public function schedule()
    {
        return $this->belongsTo(KomSchedule::class, 'kom_schedule_id');
        // Ganti Schedule::class sesuai model jadwal kamu
    }

    // Relasi ke peserta kelas (kelas_tutorial)
    public function peserta()
    {
        return $this->belongsTo(KelasTutorial::class, 'kelas_tutorial_id');
        // Ganti KelasTutorial::class sesuai model kamu
    }
}
