<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KmdUserVideoProgress extends Model
{
    protected $connection = 'mysql';
    protected $table = 'kmd_user_progress_video';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'video_id',
        'status',
        'date_publish',
        'is_watched',
        'answered_question',
        'is_correct',
        'watched_at',
        'last_time',
    ];

    protected $casts = [
        'is_watched' => 'boolean',
        'answered_question' => 'boolean',
        'is_correct' => 'boolean',
        'date_publish' => 'datetime',
        'watched_at' => 'datetime',
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

    public function user()
    {
        return $this->belongsTo(KomUser::class, 'user_id');
    }

    public function video()
    {
        return $this->belongsTo(KmdVideo::class, 'video_id');
    }
}
