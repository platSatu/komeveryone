<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class KmdUserAbsensi extends Model
{
    use HasUuids;
    protected $connection = 'mysql';
    protected $table = 'kmd_user_absensi';

    protected $fillable = [
        'user_id',
        'userclasstutorial_id',
        'hadir',
        'tidak_hadir',
        'status',
    ];

    protected $casts = [
        'hadir' => 'boolean',
        'tidak_hadir' => 'boolean',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(KomUser::class, 'user_id');
    }

    // Relasi ke UserClassTutorial
    public function userClassTutorial()
    {
        return $this->belongsTo(KmdUserClassTutorial::class, 'userclasstutorial_id');
    }
}
