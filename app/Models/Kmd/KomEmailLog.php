<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;

/** 5 Nov 2026 — satu baris = satu email aplikasi (lihat KomEmailLogRecorder). */
class KomEmailLog extends Model
{
    protected $connection = 'mysql';
    protected $table = 'kom_email_log';

    public const ANTRE = 'antre';
    public const TERKIRIM = 'terkirim';
    public const GAGAL = 'gagal';

    public const STATUS_LABEL = [
        self::TERKIRIM => 'Terkirim',
        self::GAGAL => 'Gagal',
        self::ANTRE => 'Di antrean',
    ];

    protected $fillable = ['jenis', 'penerima', 'judul', 'status', 'job_uuid', 'error', 'terkirim_at'];

    protected $casts = ['terkirim_at' => 'datetime'];
}
