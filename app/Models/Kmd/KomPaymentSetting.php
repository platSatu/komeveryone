<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/** kom_v02 — setting payment gateway (key-value). Lihat App\Services\Payment\PaymentGatewaySetting. */
class KomPaymentSetting extends Model
{
    use HasUuids;

    protected $connection = 'mysql';
    protected $table = 'kom_payment_settings';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['key', 'value', 'is_secret', 'updated_by'];

    protected $casts = ['is_secret' => 'boolean'];
}
