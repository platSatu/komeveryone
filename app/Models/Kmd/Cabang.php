<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cabang extends Model
{
    // Nama tabel eksplisit
    protected $connection = 'mysql';
    
    protected $table = 'kmd_cabangs';

    // UUID sebagai primary key
    protected $keyType = 'string';
    public $incrementing = false;

    // Fillable fields untuk mass assignment
    protected $fillable = [
        'id',
        'name',
        'status',
    ];

    // Otomatis generate UUID saat create record baru
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // Relasi ke admin cabang (users dengan role admin_cabang)
    public function admins()
    {
        return $this->hasMany(KomUser::class, 'cabang_id')->where('role', 'admin_cabang');
    }
}
