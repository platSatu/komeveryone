<?php

namespace App\Models\Kom;


use App\Models\Rayon;
use Illuminate\Database\Eloquent\Model;

class KomPertanyaanUser extends Model
{
    protected $table = 'kom_pertanyaan_user';

    protected $fillable = [
        'kom_user_id',
        'kom_schedules_id',
        'kom_class_id',
        'kom_moduls_id',
        'rayons_id',
        'pertanyaan',
        'tgl_pertanyaan',
        'status',
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(KomUser::class, 'kom_user_id');
    }

    // Relasi ke rayon
    public function rayon()
    {
        return $this->belongsTo(Rayon::class, 'rayons_id');
    }

    // Relasi ke kelas
    public function kelas()
    {
        return $this->belongsTo(KomClass::class, 'kom_class_id');
    }

    // Relasi ke modul
    public function modul()
    {
        return $this->belongsTo(KomModul::class, 'kom_moduls_id');
    }

    public function schedule()
    {
        return $this->belongsTo(KomSchedule::class, 'kom_schedules_id');
    }

    public function jawabans()
    {
        return $this->hasMany(\App\Models\Kom\KomJawaban::class, 'pertanyaan_id');
    }

}
