<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KmdBooks extends Model
{
    protected $connection = 'mysql';
    protected $table = 'kmd_books';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'no_books',
        'certificate_id',
        'class_id',
        'date_publish',
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

    public function certificate()
    {
        return $this->belongsTo(KmdCertificates::class, 'certificate_id');
    }

    public function class()
    {
        return $this->belongsTo(KmdClass::class, 'class_id');
    }
}
