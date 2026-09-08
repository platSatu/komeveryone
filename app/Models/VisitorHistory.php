<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Satu baris = satu kunjungan halaman (dicatat App\Http\Middleware\
 * TrackVisitor). Dipakai App\Http\Controllers\Kmd\Superadmin\
 * VisitorStatisticController buat halaman "Statistik Pengunjung" di menu
 * Superadmin -- jumlah kunjungan per minggu/bulan/tahun + halaman paling
 * banyak dikunjungi.
 */
class VisitorHistory extends Model
{
    protected $fillable = [
        'ip_address',
        'browser',
        'page_url',
        'method',
        'referrer',
        'status_code',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];
}
