<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;

class KmdUserModul extends Model
{
    protected $connection = 'mysql';
    protected $table = 'kmd_user_module';

    protected $keyType = 'string';      // Karena menggunakan UUID
    public $incrementing = false;       // UUID bukan auto increment

    protected $fillable = [
        'id',
        'user_id',
        'module_id',
        'is_completed',
        'completed_at',
        'tutorial_unlocked',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'tutorial_unlocked' => 'boolean',
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
}
