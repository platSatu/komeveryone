<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Database\Eloquent\SoftDeletes; // HAPUS BARIS INI
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class KmdClass extends Model
{
    protected $connection = 'mysql';

    protected $table = 'kmd_classes';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'status',
        'code', // kom_v02
        'sort_order', // kom_v02
        'prerequisite_class_id', // kom_v02
        'cover_image', // kom_v02
    ];

    protected $casts = [
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // 'deleted_at' => 'datetime', // HAPUS BARIS INI
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }

            // kom_v02: kode URL & urutan katalog otomatis dari nama (mis. "KOM 200" → kom-200 / 200)
            if (empty($model->code)) {
                $code = Str::slug($model->name);
                if (static::where('code', $code)->exists()) {
                    $code .= '-'.substr($model->id, 0, 8);
                }
                $model->code = $code;
            }
            if (empty($model->sort_order)) {
                $model->sort_order = (int) preg_replace('/\D/', '', (string) $model->name);
            }
        });

        // kom_v02: nama kelas otomatis tersimpan sebagai terjemahan Bahasa Indonesia
        static::created(function ($model) {
            KmdClassTranslation::firstOrCreate(
                ['class_id' => $model->id, 'language_code' => Language::defaultCode()],
                ['name' => $model->name]
            );
        });
    }

    // Scopes
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('name', 'LIKE', "%{$search}%");
    }

    public function scopeByStatus(Builder $query, bool $status): Builder
    {
        return $query->where('status', $status);
    }

    // UUID Encryption/Decryption
    public function getEncryptedIdAttribute(): string
    {
        return Crypt::encrypt($this->id);
    }

    public static function decryptId(string $encryptedId): string
    {
        try {
            return Crypt::decrypt($encryptedId);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid encrypted ID');
        }
    }

    public static function findByEncryptedId(string $encryptedId): ?self
    {
        $id = self::decryptId($encryptedId);
        return self::find($id);
    }

    public static function findByEncryptedIdOrFail(string $encryptedId): self
    {
        $id = self::decryptId($encryptedId);
        return self::findOrFail($id);
    }

    // ===================== kom_v02 =====================

    public function translations()
    {
        return $this->hasMany(KmdClassTranslation::class, 'class_id');
    }

    public function prerequisite()
    {
        return $this->belongsTo(KmdClass::class, 'prerequisite_class_id');
    }

    public function moduls()
    {
        return $this->hasMany(KmdModuls::class, 'kom_class_id');
    }

    /** Terjemahan untuk bahasa tertentu, fallback ke Bahasa Indonesia, lalu nama asli. */
    public function translation(?string $languageCode = null): ?KmdClassTranslation
    {
        $languageCode ??= app()->getLocale();
        $rows = $this->relationLoaded('translations') ? $this->translations : $this->translations()->get();

        return $rows->firstWhere('language_code', $languageCode)
            ?? $rows->firstWhere('language_code', Language::defaultCode());
    }

    public function translatedName(?string $languageCode = null): string
    {
        return $this->translation($languageCode)?->name ?? $this->name;
    }

    public function translatedDescription(?string $languageCode = null): ?string
    {
        return $this->translation($languageCode)?->description;
    }
}
