<?php

namespace App\Models\Kom;

use Illuminate\Database\Eloquent\Model;

class KomUserJawabanPertanyaan extends Model
{
    protected $table = 'kom_user_jawaban_pertanyaan';

    protected $fillable = [
        'kom_user_id', 'pertanyaan_id', 'jawaban_id', 'jawaban_benar',
    ];

    public function komUser()
    {
        return $this->belongsTo(KomUser::class, 'kom_user_id');
    }

    public function user()
    {
        return $this->belongsTo(KomUser::class, 'kom_user_id', 'id');
    }

    public function pertanyaan()
    {
        return $this->belongsTo(KomPertanyaan::class, 'pertanyaan_id');
    }

    public function jawaban()
    {
        return $this->belongsTo(KomJawaban::class, 'jawaban_id');
    }
}
