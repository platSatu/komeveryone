<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KomPayment extends Model
{
    protected $table = 'kom_payments';

    protected $fillable = [
        'booking_id', 'amount', 'status', 'payment_gateway', 'callback_response',
    ];
}
