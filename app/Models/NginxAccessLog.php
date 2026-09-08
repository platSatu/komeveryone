<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Satu baris hasil parse access.log nginx. Diisi oleh
 * App\Console\Commands\ImportNginxLog. Ditambahkan 8 September 2026.
 */
class NginxAccessLog extends Model
{
    protected $fillable = [
        'ip_address',
        'method',
        'path',
        'status_code',
        'bytes_sent',
        'referrer',
        'user_agent',
        'requested_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
    ];
}
