<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KomCertificate extends Model
{
    protected $table = 'kom_certificates';

    protected $fillable = [
        'no_sertifikat', 'tgl_sertifikat', 'status',
    ];
}
