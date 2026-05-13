<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    protected $table = 'cabang';

    // Tentukan kolom yang dapat diisi (fillable)
    protected $fillable = ['name'];
}
