<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KomClass extends Model
{
    protected $table = 'kom_class';

    protected $fillable = [
        'name',
        'status'
    ];

    public function users()
    {
        return $this->hasMany(KomUser::class, 'kom_class_id', 'id');
    }

}
