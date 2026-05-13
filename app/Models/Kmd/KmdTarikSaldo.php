<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class KmdTarikSaldo extends Model
{
    use HasUuids;

    protected $connection = 'mysql';
    protected $table = 'kmd_tarik_saldo';

    protected $fillable = [
        'user_id',
        'cabang_id',
        'donasi_id',
        'userclasstutorial_id',
        'tanggal_penarikan',
        'jumlah',
        'status',
        'catatan',
        // 'approved_by',
        // 'approved_at',
        // 'rejected_reason'
    ];

    protected $casts = [
        'tanggal_penarikan' => 'datetime',
        'approved_at' => 'datetime',
        'jumlah' => 'decimal:2',
    ];

    // Relasi ke User (yang menarik saldo)
    public function user()
    {
        return $this->belongsTo(KomUser::class, 'user_id');
    }

    // Relasi ke UserClassTutorial
    public function userClassTutorial()
    {
        return $this->belongsTo(KmdUserClassTutorial::class, 'userclasstutorial_id');
    }

    // Relasi ke Cabang
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    // Relasi ke User yang approve
    public function approvedBy()
    {
        return $this->belongsTo(KomUser::class, 'approved_by');
    }

    /**
     * Get formatted amount
     */
    public function getFormattedJumlahAttribute()
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-warning',
            'approved' => 'bg-info',
            'completed' => 'bg-success',
            'rejected' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak',
            default => ucfirst($this->status)
        };
    }
}
