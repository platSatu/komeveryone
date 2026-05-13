<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KmdUserQuestions extends Model
{
    protected $connection = 'mysql';
    protected $table = 'kmd_user_questions';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'question_id',
        'answer_id',
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

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(KomUser::class, 'user_id');
    }

    // Relasi ke pertanyaan
    public function question()
    {
        return $this->belongsTo(KmdQuestions::class, 'question_id');
    }

    // Relasi ke jawaban
    public function answer()
    {
        return $this->belongsTo(KmdAnswer::class, 'answer_id');
    }
}
