<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class KmdDonasi extends Model
{
    use HasUuids;
    protected $connection = 'mysql';
    protected $table = 'kmd_donations';

    protected $fillable = [
        'user_id',
        'userclasstutorial_id',
        'user_class_progress_id',
        'modul_id',
        'category_diklat',
        'class_id',
        'cabang_id',
        'order_id',
        'amount',
        'fee_payment_gateway',
        'payment_type',
        'transaction_time',
        'transaction_status',
        'payment_token',
        'status',
    ];

    protected $casts = [
        'transaction_time' => 'datetime',
        'amount' => 'decimal:2',
        'fee_payment_gateway' => 'decimal:2',
    ];

    // Relasi ke User (yang berdonasi)
    public function user()
    {
        return $this->belongsTo(KomUser::class, 'user_id');
    }

    // Relasi ke UserClassTutorial
    public function userClassTutorial()
    {
        return $this->belongsTo(KmdUserClassTutorial::class, 'userclasstutorial_id');
    }

    // Relasi ke Modul
    public function modul()
    {
        return $this->belongsTo(KmdModuls::class, 'modul_id');
    }

    // Relasi ke Kelas
    public function kelas()
    {
        return $this->belongsTo(KmdClass::class, 'class_id');
    }

    // Relasi ke Cabang
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function userclassprogress()
    {
        // Ubah model & foreign key jika perlu
        return $this->belongsTo(UserClassProgress::class, 'userclasstutorial_id', 'id');
    }


    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get formatted fee
     */
    public function getFormattedFeeAttribute()
    {
        return 'Rp ' . number_format($this->fee_payment_gateway, 0, ',', '.');
    }

    /**
     * Get net amount (amount - fee)
     */
    public function getNetAmountAttribute()
    {
        return $this->amount - $this->fee_payment_gateway;
    }

    /**
     * Get formatted net amount
     */
    public function getFormattedNetAmountAttribute()
    {
        return 'Rp ' . number_format($this->net_amount, 0, ',', '.');
    }

    /**
     * Get payment type label
     */
    public function getPaymentTypeLabelAttribute()
    {
        $labels = [
            'bank_transfer' => 'Transfer Bank',
            'credit_card' => 'Kartu Kredit',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'qris' => 'QRIS',
            'indomaret' => 'Indomaret',
            'alfamart' => 'Alfamart',
        ];

        return $labels[$this->payment_type] ?? ucfirst($this->payment_type);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'paid' => 'bg-success',
            'pending' => 'bg-warning',
            'failed' => 'bg-danger',
            'cancelled' => 'bg-secondary',
            default => 'bg-info'
        };
    }
}
