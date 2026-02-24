<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class HackerNewsApi
{
    private const BASE_URL = 'https://hacker-news.firebaseio.com/v0';

    public function topStories(): array
    {
        return $this->fetchStoryIds('/topstories.json');
    }

    public function newStories(): array
    {
        return $this->fetchStoryIds('/newstories.json');
    }

    public function bestStories(): array
    {
        return $this->fetchStoryIds('/beststories.json');
    }

    public function item(int $id): ?array
    {
        usleep(100_000); // 100ms rate limit

        $response = Http::retry(3, 500)
            ->timeout(10)
            ->get(self::BASE_URL."/item/{$id}.json");

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }

    private function fetchStoryIds(string $path): array
    {
        $response = Http::retry(3, 500)
            ->timeout(10)
            ->get(self::BASE_URL.$path);

        if ($response->failed()) {
            return [];
        }

        return $response->json() ?? [];
    }
}
