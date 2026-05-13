<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KmdAnswer extends Model
{
    protected $connection = 'mysql';
    protected $table = 'kmd_answereds';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'questions_id',
        'answer',
        'is_true',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // Relasi ke pertanyaan
    public function question()
    {
        return $this->belongsTo(KmdQuestions::class, 'questions_id');
    }

    
}
