<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'parent_id',
        'story_id',
        'by',
        'text',
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

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
