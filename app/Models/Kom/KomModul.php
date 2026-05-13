<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KomModul extends Model
{
    protected $table = 'kom_moduls';

    protected $fillable = [
        'title',
        'description',
        'order',
        'status',
        'kom_class_id',
    ];

    public function komClass()
    {
        return $this->belongsTo(KomClass::class, 'kom_class_id');
    }

    public function komVideos()
    {
        return $this->hasMany(KomVideo::class, 'kom_modul_id');
    }

    // KomModul.php
    public function videos()
    {
        return $this->hasMany(KomVideo::class, 'kom_modul_id');
    }

    public function getOrderedVideos()
    {
        // Mengambil video berdasarkan urutan
        return $this->hasMany(KomVideo::class)->orderBy('urutan_video');  // Urutkan berdasarkan urutan_video
    }


}
