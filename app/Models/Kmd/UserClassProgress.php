<?php
namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserClassProgress extends Model
{
     protected $connection = 'mysql';
    protected $table = 'kmd_user_class_progress';

    public $incrementing = false; // Karena pakai UUID sebagai primary key
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'kelas_tutorial_id',
        'pengajar_id',
        'class_id',
        'modul_id',
        'rayon_id',
        'donasi_id',
        'pertanyaan_json',
        'step',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        // Generate UUID otomatis saat membuat model baru
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
