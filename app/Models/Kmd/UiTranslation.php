<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * kom_v02 — Terjemahan teks tampilan (tabel: ui_translations).
 * Diisi superadmin lewat menu "Terjemahan Tampilan".
 */
class UiTranslation extends Model
{
    protected $connection = 'mysql';
    protected $table = 'ui_translations';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'group',
        'key',
        'language_code',
        'value',
    ];

    public static function cacheKey(string $languageCode): string
    {
        return 'ui_translations.'.$languageCode;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    protected static function booted(): void
    {
        $forget = fn (self $row) => Cache::forget(self::cacheKey($row->language_code));
        static::saved($forget);
        static::deleted($forget);
    }
}
