<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KomUserProgress extends Model
{
    protected $table = 'kom_sertifikat';

    protected $fillable = [
        'user_id',
        'tingkat',
        'nomor_sertifikat',
        'qr_code_path',
        'tanggal_terbit',
    ];
}
