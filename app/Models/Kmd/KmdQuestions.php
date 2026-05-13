<?php

namespace App\Models\Kmd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KmdQuestions extends Model
{
    protected $connection = 'mysql';
    protected $table = 'kmd_questions';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'questions',
        'status',
    ];

    // Auto-generate UUID saat create
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function answers()
    {
        return $this->hasMany(KmdAnswer::class, 'questions_id'); // sesuaikan nama foreign key
    }
}
