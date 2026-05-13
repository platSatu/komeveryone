<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KomVideoPertanyaan extends Model
{
    protected $table = 'kom_video_pertanyaan';

    protected $fillable = [
        'kom_video_id',
        'pertanyaan_id',
    ];

    public function komVideo()
    {
        return $this->belongsTo(KomVideo::class, 'kom_video_id');
    }

    public function video()
    {
        return $this->belongsTo(KomVideo::class, 'kom_video_id'); // Ganti 'kom_video_id' dengan nama kolom yang sesuai
    }

    // public function videos()
    // {
    //     return $this->hasMany(KomVideo::class, 'kom_modul_id', 'kom_modul_id');
    // }

    public function pertanyaan()
    {
        return $this->belongsTo(KomPertanyaan::class, 'pertanyaan_id');
    }

    // KomPertanyaan.php
public function jawaban()
{
    return $this->hasMany(KomJawaban::class, 'pertanyaan_id');
}

public function videos()
{
    return $this->belongsToMany(KomVideo::class, 'kom_video_pertanyaan', 'pertanyaan_id', 'kom_video_id');
}

}
