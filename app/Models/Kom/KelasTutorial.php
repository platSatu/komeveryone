<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KelasTutorial extends Model
{
    protected $table = 'kom_kelas_tutorial';

    protected $fillable = [
        'kom_user_id',
        'kom_schedules_id',
        'kom_moduls_id',
        'kom_class_id',
        'rayons_id',
        'tgl_booking',
        'status',
        'hadir',
        'tidak_hadir',
        'status_kelas',
    ];

    // Relasi
    public function user()
    {
        return $this->belongsTo(KomUser::class, 'kom_user_id');
    }

    public function schedule()
    {
        return $this->belongsTo(KomSchedule::class, 'kom_schedules_id');
    }

    public function modul()
    {
        return $this->belongsTo(KomModul::class, 'kom_moduls_id');
    }

    public function class()
    {
        return $this->belongsTo(KomClass::class, 'kom_class_id');
    }

    public function kelas()
    {
        return $this->belongsTo(KomClass::class, 'kom_class_id');
    }

    public function rayon()
    {
        return $this->belongsTo(Rayon::class, 'rayons_id');
    }


    // KomKelasTutorial.php
    public function pertanyaan()
    {
        return $this->hasMany(KomPertanyaanUser::class, 'kom_user_id', 'kom_user_id');
    }



}
