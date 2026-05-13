<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KomUserVideoProgress extends Model
{
    protected $table = 'kom_user_video_progress';

    protected $fillable = [
        'kom_user_id', 'kom_video_id', 'is_locked',
    ];

    public function user()
    {
        return $this->belongsTo(KomUser::class, 'kom_user_id', 'id');
    }


    public function komUser()
    {
        return $this->belongsTo(KomUser::class, 'kom_user_id');
    }

    public function video()
    {
        return $this->belongsTo(KomVideo::class, 'kom_video_id');
    }
}
