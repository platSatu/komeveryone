<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Posisi baca terakhir per file log, dipakai App\Console\Commands\ImportNginxLog
 * supaya import incremental (bukan baca ulang dari awal tiap kali jalan).
 * Ditambahkan 8 September 2026.
 */
class NginxLogImportState extends Model
{
    protected $fillable = [
        'log_path',
        'last_inode',
        'last_offset',
    ];

    protected $casts = [
        'last_inode' => 'integer',
        'last_offset' => 'integer',
    ];
}
