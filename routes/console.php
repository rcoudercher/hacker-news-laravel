<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

foreach (['top', 'new', 'best'] as $source) {
    $frequency = config("hackernews.schedule.{$source}.frequency");
    $limit = config("hackernews.schedule.{$source}.limit");

    Schedule::command("hn:scrape --source={$source} --limit={$limit}")
        ->cron("*/{$frequency} * * * *")
        ->withoutOverlapping();
}
