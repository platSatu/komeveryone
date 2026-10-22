<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KmdUserRekening extends Model
{
    protected $connection = 'mysql';
    protected $table = 'kmd_user_rekening';

    public $incrementing = false; // Karena pakai UUID, bukan auto increment
    protected $keyType = 'string'; // PK bertipe string (UUID)

    protected $fillable = [
        'user_id',
        'cabang_id',
        'nama_rek',
        'no_rek',
        'nama_bank',
        'cabang_bank',
    ];

    /** bank_code & bizbos_* diisi lewat forceFill setelah verifikasi Bizbos. */
    protected $casts = [
        'bizbos_verified_at' => 'datetime',
    ];

    public const KOLOM_VERIFIKASI = ['bank_code', 'bizbos_account_id', 'bizbos_verified_name', 'bizbos_verified_at'];

    /** Rekening sudah dicek ke bank lewat Bizbos dan siap menerima dana. */
    public function terverifikasi(): bool
    {
        return (bool) ($this->bizbos_account_id && $this->bizbos_verified_at);
    }

    public function bizbosExternalId(): string
    {
        return 'kom-user-' . $this->user_id;
    }

    // Otomatis generate UUID saat membuat record baru
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(KomUser::class, 'user_id', 'id');
    }

    // Relasi ke cabang
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'id');
    }
        /**
     * Get masked nomor rekening untuk keamanan
     */
            
       
    public function getFormattedNoRekAttribute()
    {
        $noRek = $this->no_rek;
        
        // Format berdasarkan panjang nomor rekening
        if (strlen($noRek) >= 10) {
            return chunk_split($noRek, 4, '-');
        }
        
        return $noRek;
    }
    /**
     * Get masked nomor rekening untuk keamanan
     */
    public function getMaskedNoRekAttribute()
    {
        $noRek = $this->no_rek;
        $length = strlen($noRek);
        
        if ($length <= 4) {
            return str_repeat('*', $length);
        }
        
        // Tampilkan 2 digit pertama dan 2 digit terakhir
        $start = substr($noRek, 0, 2);
        $end = substr($noRek, -2);
        $middle = str_repeat('*', $length - 4);
        
        return $start . $middle . $end;
    }
    /**
     * Get full bank info
     */
    public function getFullBankInfoAttribute()
    {
        return $this->nama_bank . ' - ' . $this->cabang_bank;
    }
}
