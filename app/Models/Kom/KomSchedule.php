<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KomSchedule extends Model
{
    protected $table = 'kom_jadwal';

    protected $fillable = [
        'kom_user_id',
        'pengajar_id',
        'rayon_id',
        'kom_class_id',
        'kom_modul_id',
        'datetime',
        'description',
        'quota',
        'status',
    ];

    // Relasi ke kom_users
    public function KomUser()
    {
        return $this->belongsTo(\App\Models\Kom\KomUser::class, 'kom_user_id');
    }

    // Relasi ke rayons
    public function rayon()
    {
        return $this->belongsTo(\App\Models\Rayon::class, 'rayon_id');
    }

    // Relasi ke kom_classes
    public function komClass()
    {
        return $this->belongsTo(KomClass::class, 'kom_class_id');
    }

    // Relasi ke kom_moduls
    public function komModul()
    {
        return $this->belongsTo(KomModul::class, 'kom_modul_id');
    }

public function kelasTutorial()
{
    return $this->hasMany(KelasTutorial::class, 'kom_schedules_id');
}
   
public function pengajar()
{
    return $this->belongsTo(KomUser::class, 'pengajar_id');
}

public function zoom()
{
    return $this->hasOne(KomZoom::class, 'kom_schedules_id');
}


}
