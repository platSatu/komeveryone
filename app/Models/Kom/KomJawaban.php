<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KomJawaban extends Model
{
    protected $table = 'kom_jawaban';

    protected $fillable = [
        'pertanyaan_id', 'jawaban', 'is_benar',
    ];

    public function pertanyaan()
    {
        return $this->belongsTo(KomPertanyaan::class, 'pertanyaan_id');
    }

    public function userJawaban()
    {
        return $this->hasMany(KomUserJawabanPertanyaan::class, 'jawaban_id');
    }
}
