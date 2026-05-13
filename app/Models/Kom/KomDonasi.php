<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KomDonasi extends Model
{
    protected $table = 'kom_donasi';

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'partner_service_id',
        'customer_no',
        'virtual_account_no',
        'virtual_account_name',
        'trx_id',
        'payment_request_id',
        'paid_amount_value',
        'fee',
        'donate',
        'paid_amount_currency',
        'virtual_account_email',
        'virtual_account_phone',
        'payment_status',
        'donation_type',
        'kom_user_id',
        'kom_rayons_id',
        'kom_schedules_id',
        'kom_moduls_id',
    ];

    // Definisikan relasi dengan model KomUser (relasi Many to One)
    public function komUser()
    {
        return $this->belongsTo(\App\Models\Kom\KomUser::class, 'kom_user_id');
    }

    // Definisikan relasi dengan model Rayon (relasi Many to One)
    public function rayon()
    {
        return $this->belongsTo('App\Models\Rayon', 'kom_rayons_id');
    }

    // Definisikan relasi dengan model KomSchedule (relasi Many to One)
    public function komSchedule()
    {
        return $this->belongsTo('App\Models\Kom\KomSchedule', 'kom_schedules_id');
    }

    // Definisikan relasi dengan model KomModul (relasi Many to One)
    public function komModul()
    {
        return $this->belongsTo('App\Models\Kom\KomModul', 'kom_moduls_id');
    }

    public function zoom()
    {
        return $this->hasOne(KomZoom::class, 'kom_schedules_id', 'kom_schedules_id')
            ->where('kom_moduls_id', $this->kom_moduls_id)
            ->where('kom_class_id', $this->kom_class_id);
    }

    public static function getSaldoByUserRayon($rayonId, $userId = null)
    {
        return self::where('kom_rayons_id', $rayonId)
            ->where('payment_status', 'paid')
            ->sum('donate');
    }


}
