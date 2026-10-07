<?php

namespace App\Translation;

use App\Models\Kmd\UiTranslation;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * kom_v02 — Terjemahan tampilan dari database (tabel ui_translations).
 *
 * Cara pakai di view / controller: tulis teks Bahasa Indonesia apa adanya,
 *     {{ __('Kelas Saya') }}
 * - Bahasa Indonesia  : tampil "Kelas Saya" (teks aslinya).
 * - Bahasa lain (en)  : dicari di ui_translations (key = 'Kelas Saya', language_code = 'en').
 *                       Jika belum diisi superadmin → tetap tampil Bahasa Indonesia.
 *
 * Loader ini membungkus loader file bawaan Laravel, jadi file lang/ tetap berfungsi.
 */
class DatabaseTranslationLoader implements Loader
{
    public function __construct(protected Loader $fileLoader)
    {
    }

    public function load($locale, $group, $namespace = null)
    {
        $lines = $this->fileLoader->load($locale, $group, $namespace);

        // Hanya untuk terjemahan JSON-style: __('Teks lengkap')
        if ($group === '*' && $namespace === '*') {
            return array_merge($lines, $this->databaseLines($locale));
        }

        return $lines;
    }

    protected function databaseLines(string $locale): array
    {
        try {
            return Cache::remember(UiTranslation::cacheKey($locale), 3600, function () use ($locale) {
                if (! Schema::hasTable('ui_translations')) {
                    return [];
                }

                return UiTranslation::query()
                    ->where('language_code', $locale)
                    ->whereNotNull('value')
                    ->where('value', '!=', '')
                    ->pluck('value', 'key')
                    ->all();
            });
        } catch (\Throwable $e) {
            // Database belum siap (mis. saat migrate) → jangan sampai halaman error
            Log::warning('DatabaseTranslationLoader: '.$e->getMessage());

            return [];
        }
    }

    public function addNamespace($namespace, $hint)
    {
        $this->fileLoader->addNamespace($namespace, $hint);
    }

    public function addJsonPath($path)
    {
        $this->fileLoader->addJsonPath($path);
    }

    public function namespaces()
    {
        return $this->fileLoader->namespaces();
    }

    public function __call($method, $parameters)
    {
        return $this->fileLoader->{$method}(...$parameters);
    }
}
