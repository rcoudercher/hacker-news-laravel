<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Comment;
use App\Models\Story;
use Illuminate\Console\Command;

class HackerNewsStats extends Command
{
    protected $signature = 'hn:stats';

    protected $description = 'Show Hacker News scraper statistics';

    public function handle(): int
    {
        $this->table(['Metric', 'Count'], [
            ['Stories', Story::count()],
            ['Comments', Comment::count()],
            ['Articles (total)', Article::count()],
            ['Articles (success)', Article::where('fetch_status', 'success')->count()],
            ['Articles (failed)', Article::where('fetch_status', 'failed')->count()],
            ['Articles (timeout)', Article::where('fetch_status', 'timeout')->count()],
            ['Articles (skipped)', Article::where('fetch_status', 'skipped')->count()],
            ['Articles (pending)', Article::where('fetch_status', 'pending')->count()],
        ]);

        return self::SUCCESS;
    }
}
