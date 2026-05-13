<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KomClassBooking extends Model
{
    protected $table = 'kom_class_bookings';

    protected $fillable = [
        'class_id', 'user_id', 'pertanyaan_1', 'pertanyaan_2', 'pertanyaan_3', 'status',
    ];
}
