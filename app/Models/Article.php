<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $fillable = [
        'story_id',
        'url',
        'content',
        'content_type',
        'fetch_status',
        'fetch_error',
        'fetch_duration_ms',
    ];

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }
}
