<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KomVideo extends Model
{
    protected $table = 'kom_videos';

    protected $fillable = [
        'name', 'description', 'videos', 'url_video', 'urutan_video', 'durasi_video', 'status', 'kom_modul_id',
    ];

    public function komModul()
    {
        return $this->belongsTo(KomModul::class, 'kom_modul_id');
    }

    public function modul()
    {
        return $this->belongsTo(KomModul::class);
    }

    public function pertanyaan()
    {
        return $this->belongsToMany(KomPertanyaan::class, 'kom_video_pertanyaan', 'kom_video_id', 'pertanyaan_id');
    }

    public function userProgress()
    {
        return $this->hasMany(KomUserVideoProgress::class, 'kom_video_id');
    }
}
