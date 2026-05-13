<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;

class KmdUserTutorialStatus extends Model
{
    protected $connection = 'mysql';
    protected $table = 'kmd_user_tutorial_status';

    protected $keyType = 'string';      // Karena menggunakan UUID
    public $incrementing = false;       // UUID bukan auto increment

    protected $fillable = [
        'id',
        'user_id',
        'module_id',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(KomUser::class, 'user_id', 'id');
    }

    // Relasi ke modul
    public function module()
    {
        return $this->belongsTo(KmdModuls::class, 'module_id', 'id');
    }

    public function modul()
    {
        return $this->belongsTo(KmdModuls::class, 'module_id');
    }
}
