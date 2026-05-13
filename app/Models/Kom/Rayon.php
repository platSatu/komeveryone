<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class Rayon extends Model
{
    protected $table = 'rayons';

    protected $fillable = [
        'name',
        'status',
    ];
}
