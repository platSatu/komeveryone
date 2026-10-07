<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * kom_v02 — Pendaftaran user ke kelas (tabel: kmd_user_classes).
 *
 * Satu baris = user mengikuti satu kelas (KOM 100 / 200 / ...) dengan satu bahasa.
 * Bahasa terkunci selama status = 'active'.
 */
class KmdUserClass extends Model
{
    protected $connection = 'mysql';
    protected $table = 'kmd_user_classes';

    protected $keyType = 'string';
    public $incrementing = false;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'id',
        'user_id',
        'class_id',
        'language_code',
        'book_id',
        'status',
        'certificate_id',
        'enrolled_at',
        'completed_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function user()
    {
        return $this->belongsTo(KomUser::class, 'user_id');
    }

    public function kelas()
    {
        return $this->belongsTo(KmdClass::class, 'class_id');
    }

    public function book()
    {
        return $this->belongsTo(KmdBooks::class, 'book_id');
    }

    public function certificate()
    {
        return $this->belongsTo(KmdCertificates::class, 'certificate_id');
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_code', 'code');
    }
}
