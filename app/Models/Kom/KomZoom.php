<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KomZoom extends Model
{
    protected $table = 'kom_zoom';

    protected $fillable = [
        'kom_user_id',
        'kom_moduls_id',
        'kom_class_id',
        'kom_schedules_id',
        'rayons_id',
        'description',
        'status',
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(KomUser::class, 'kom_user_id');
    }

    // Relasi ke modul
    public function modul()
    {
        return $this->belongsTo(KomModul::class, 'kom_moduls_id');
    }

    // Relasi ke kelas
    public function kelas()
    {
        return $this->belongsTo(KomClass::class, 'kom_class_id');
    }

    // Relasi ke jadwal
    public function schedule()
    {
        return $this->belongsTo(KomSchedule::class, 'kom_schedules_id');
    }

    // Relasi ke rayon
    public function rayon()
    {
        return $this->belongsTo(Rayon::class, 'rayons_id');
    }
}
