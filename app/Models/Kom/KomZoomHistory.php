<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KomZoomHistory extends Model
{
    protected $table = 'kom_zoom_histories';

    protected $fillable = [
        'kom_user_id',
        'kom_donasi_id',
        'kom_zoom_id',
        'zoom_link',
        'kom_rayons_id',
        'sent_at',
    ];

    public function user()
    {
        return $this->belongsTo(KomUser::class, 'kom_user_id');
    }

    public function donasi()
    {
        return $this->belongsTo(KomDonasi::class, 'kom_donasi_id');
    }

    public function zoom()
    {
        return $this->belongsTo(KomZoom::class, 'kom_zoom_id');
    }
}
