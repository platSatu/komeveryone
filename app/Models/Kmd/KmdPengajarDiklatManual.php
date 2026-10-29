<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/** Pengajar yang ditandai manual sudah ikut Diklat Tutor (lihat migration 2026_10_29_000014). */
class KmdPengajarDiklatManual extends Model
{
    protected $connection = 'mysql';

    protected $table = 'kmd_pengajar_diklat_manual';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['user_id', 'category_diklat', 'catatan', 'created_by'];

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

    public function pembuat()
    {
        return $this->belongsTo(KomUser::class, 'created_by');
    }
}
