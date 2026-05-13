<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KomBook extends Model
{
    protected $table = 'kom_books';

    protected $fillable = [
        'kom_user_id',
        'kom_class_id',
        'kom_certificate_id',
        'kode_buku',
        'tgl_aktifasi',
        'status',
    ];

    /**
     * Relasi ke user yang memiliki buku.
     */
    public function komUser()
    {
        return $this->belongsTo(KomUser::class, 'kom_user_id'); // Pastikan 'kom_user_id' adalah kolom yang benar
    }

    // Relasi dengan KomClass
    public function komClass()
    {
        return $this->belongsTo(KomClass::class, 'kom_class_id'); // Pastikan 'kom_class_id' adalah kolom yang benar
    }

    // Relasi dengan KomCertificate
    public function komCertificate()
    {
        return $this->belongsTo(KomCertificate::class, 'kom_certificate_id'); // Pastikan 'kom_certificate_id' adalah kolom yang benar
    }
}
