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
}
