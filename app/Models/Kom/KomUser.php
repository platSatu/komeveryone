<?php

namespace App\Models\Kom;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class KomUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'kom_users';

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'status',
        'tanggal_daftar',
        'rayon_id',
        'asal_gereja',
        'email_verification_token',
        'email_verification_sent_at',
        'email_verified_at',
        'thn_lahir',
    ];

    protected $hidden = ['password'];

    public function progress()
    {
        return $this->hasMany(KomUserProgress::class, 'user_id');
    }

    public function sertifikat()
    {
        return $this->hasMany(KomCertificate::class, 'user_id');
    }

    public function rayon()
    {
        return $this->belongsTo(\App\Models\Rayon::class);
    }

    public function users()
    {
        return $this->belongsToMany(KomUser::class, 'kom_schedule_user', 'kom_schedule_id', 'kom_user_id');
    }


}
