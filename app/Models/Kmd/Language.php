<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * kom_v02 — Master bahasa (tabel: languages).
 */
class Language extends Model
{
    protected $connection = 'mysql';
    protected $table = 'languages';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'flag',
        'certificate_prefix',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    public const CACHE_KEY_ACTIVE = 'languages.active';

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
        // Daftar bahasa aktif di-cache; reset setiap ada perubahan dari superadmin.
        static::saved(fn () => Cache::forget(self::CACHE_KEY_ACTIVE));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY_ACTIVE));
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /** Bahasa aktif untuk pop-up pilih bahasa (di-cache 1 jam). */
    public static function activeList()
    {
        return Cache::remember(self::CACHE_KEY_ACTIVE, 3600, fn () => static::active()->get());
    }

    /** Kode bahasa default (Bahasa Indonesia). */
    public static function defaultCode(): string
    {
        return static::where('is_default', true)->value('code') ?? 'id';
    }
}
