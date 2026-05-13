<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KomCabang extends Model
{
    protected $table = 'kom_cabang';

    protected $fillable = [
        'nama', 'alamat', 'kota', 'provinsi', 'kontak', 'zoom_link',
    ];
}
