<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KmdQuestionVideos extends Model
{
    protected $connection = 'mysql';
    protected $table = 'kmd_question_videos';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'videos_id',
        'questions_id',
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

    // Relasi ke video
    public function video()
    {
        return $this->belongsTo(KmdVideo::class, 'videos_id');
    }

    // Relasi ke pertanyaan
    public function question()
    {
        return $this->belongsTo(KmdQuestions::class, 'questions_id');
    }

    public function answers()
    {
        return $this->hasMany(KmdAnswer::class, 'questions_id', 'questions_id');
    }

}
