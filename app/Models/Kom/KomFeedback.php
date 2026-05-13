<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KomFeedback extends Model
{
    protected $table = 'kom_feedback';

    protected $fillable = [
        'user_id', 'kelas_id', 'feedback', 'rating',
    ];
}
