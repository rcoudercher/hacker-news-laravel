<?php

namespace App\Jobs;

use App\Models\Story;
use App\Services\HackerNewsApi;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchStory implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public int $storyId,
    ) {}

    public function handle(HackerNewsApi $api): void
    {
        $item = $api->item($this->storyId);

        if (! $item || ($item['deleted'] ?? false)) {
            Story::updateOrCreate(['id' => $this->storyId], [
                'deleted' => true,
            ]);

            return;
        }

        Story::updateOrCreate(['id' => $item['id']], [
            'type' => $item['type'] ?? 'story',
            'by' => $item['by'] ?? null,
            'title' => $item['title'] ?? null,
            'url' => $item['url'] ?? null,
            'text' => $item['text'] ?? null,
            'score' => $item['score'] ?? 0,
            'descendants' => $item['descendants'] ?? 0,
            'posted_at' => isset($item['time']) ? Carbon::createFromTimestamp($item['time']) : null,
            'dead' => $item['dead'] ?? false,
            'deleted' => false,
        ]);

        // Dispatch comment fetching for kids
        $kids = $item['kids'] ?? [];
        if (! empty($kids)) {
            FetchComments::dispatch($kids, $item['id']);
        }

        // Dispatch article content fetching if URL exists
        if (! empty($item['url'])) {
            FetchArticleContent::dispatch($item['id'], $item['url'])
                ->onQueue('articles');
        }
    }
}
