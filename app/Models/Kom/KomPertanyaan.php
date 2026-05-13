<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KomPertanyaan extends Model
{
    protected $table = 'kom_pertanyaan';

    protected $fillable = [
        'pertanyaan',
    ];

    public function jawaban()
    {
        return $this->hasMany(KomJawaban::class, 'pertanyaan_id');
    }

    public function komVideos()
    {
        return $this->belongsToMany(KomVideo::class, 'kom_video_pertanyaan', 'pertanyaan_id', 'kom_video_id');
    }

    public function userJawaban()
    {
        return $this->hasMany(KomUserJawabanPertanyaan::class, 'pertanyaan_id');
    }
}
