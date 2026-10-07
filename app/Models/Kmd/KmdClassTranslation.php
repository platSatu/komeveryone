<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * kom_v02 — Nama & deskripsi kelas per bahasa (tabel: kmd_class_translations).
 */
class KmdClassTranslation extends Model
{
    protected $connection = 'mysql';
    protected $table = 'kmd_class_translations';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'class_id',
        'language_code',
        'name',
        'description',
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

    public function kelas()
    {
        return $this->belongsTo(KmdClass::class, 'class_id');
    }
}
