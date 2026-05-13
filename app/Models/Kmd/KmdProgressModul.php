<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KmdProgressModul extends Model
{
    protected $connection = 'mysql';
    protected $table = 'kmd_progress_moduls';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'moduls_id',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(KomUser::class, 'user_id');
    }

    public function modul()
    {
        return $this->belongsTo(KmdModuls::class, 'moduls_id');
    }
}
