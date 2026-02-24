<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Story extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'type',
        'by',
        'title',
        'url',
        'text',
        'score',
        'descendants',
        'posted_at',
        'dead',
        'deleted',
    ];

    protected function casts(): array
    {
        return [
            'posted_at' => 'datetime',
            'dead' => 'boolean',
            'deleted' => 'boolean',
        ];
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function article(): HasOne
    {
        return $this->hasOne(Article::class);
    }
}
