<?php

namespace App\Console\Commands;

use App\Jobs\FetchStory;
use App\Services\HackerNewsApi;
use Illuminate\Console\Command;

class ScrapeHackerNews extends Command
{
    protected $signature = 'hn:scrape
        {--source=top : Story source: top, new, or best}
        {--limit=30 : Number of stories to fetch}';

    protected $description = 'Scrape stories from Hacker News';

    public function handle(HackerNewsApi $api): int
    {
        $source = $this->option('source');
        $limit = (int) $this->option('limit');

        if (! in_array($source, ['top', 'new', 'best'])) {
            $this->error("Invalid source: {$source}. Use top, new, or best.");

            return self::FAILURE;
        }

        $storyIds = match ($source) {
            'top' => $api->topStories(),
            'new' => $api->newStories(),
            'best' => $api->bestStories(),
        };

        $storyIds = array_slice($storyIds, 0, $limit);

        foreach ($storyIds as $id) {
            FetchStory::dispatch($id);
        }

        $this->info("Dispatched ".count($storyIds)." {$source} story jobs.");

        return self::SUCCESS;
    }
}
