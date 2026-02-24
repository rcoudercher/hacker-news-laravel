<?php

namespace App\Jobs;

use App\Models\Comment;
use App\Services\HackerNewsApi;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchComments implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public array $commentIds,
        public int $storyId,
    ) {}

    public function handle(HackerNewsApi $api): void
    {
        foreach ($this->commentIds as $commentId) {
            $item = $api->item($commentId);

            if (! $item) {
                continue;
            }

            if ($item['deleted'] ?? false) {
                Comment::updateOrCreate(['id' => $commentId], [
                    'story_id' => $this->storyId,
                    'deleted' => true,
                ]);

                continue;
            }

            Comment::updateOrCreate(['id' => $item['id']], [
                'parent_id' => $item['parent'] ?? null,
                'story_id' => $this->storyId,
                'by' => $item['by'] ?? null,
                'text' => $item['text'] ?? null,
                'posted_at' => isset($item['time']) ? Carbon::createFromTimestamp($item['time']) : null,
                'dead' => $item['dead'] ?? false,
                'deleted' => false,
            ]);

            // Recursively fetch child comments
            $kids = $item['kids'] ?? [];
            if (! empty($kids)) {
                FetchComments::dispatch($kids, $this->storyId);
            }
        }
    }
}
