<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KomUserModulProgress extends Model
{
    protected $fillable = [
        'kom_user_id',
        'kom_modul_id',
        'last_video_id',
        'video_terakhir',
    ];

    public function user()
    {
        return $this->belongsTo(KomUser::class, 'kom_user_id');
    }

    public function modul()
    {
        return $this->belongsTo(KomModul::class, 'kom_modul_id');
    }

    public function lastVideo()
    {
        return $this->belongsTo(KomVideo::class, 'last_video_id');
    }
}
