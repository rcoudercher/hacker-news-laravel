<?php

namespace App\Jobs;

use App\Models\Article;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class FetchArticleContent implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;
    public int $backoff = 120;

    public function __construct(
        public int $storyId,
        public string $url,
    ) {
        $this->onQueue('articles');
    }

    public function handle(): void
    {
        // Skip if already successfully fetched
        $existing = Article::where('story_id', $this->storyId)
            ->where('fetch_status', 'success')
            ->exists();

        if ($existing) {
            return;
        }

        $article = Article::updateOrCreate(
            ['story_id' => $this->storyId],
            ['url' => $this->url, 'fetch_status' => 'pending'],
        );

        $startTime = microtime(true);

        try {
            $response = Http::timeout(15)
                ->connectTimeout(10)
                ->withHeaders([
                    'User-Agent' => 'HackerNewsScraper/1.0',
                    'Accept' => 'text/html',
                ])
                ->get($this->url);

            $durationMs = (int) ((microtime(true) - $startTime) * 1000);
            $contentType = $response->header('Content-Type') ?? '';

            // Skip non-HTML content
            if (! str_contains($contentType, 'text/html') && ! str_contains($contentType, 'text/plain')) {
                $article->update([
                    'content_type' => $contentType,
                    'fetch_status' => 'skipped',
                    'fetch_error' => 'Non-HTML content type',
                    'fetch_duration_ms' => $durationMs,
                ]);

                return;
            }

            if ($response->failed()) {
                $article->update([
                    'content_type' => $contentType,
                    'fetch_status' => 'failed',
                    'fetch_error' => "HTTP {$response->status()}",
                    'fetch_duration_ms' => $durationMs,
                ]);

                return;
            }

            $html = $response->body();
            $text = $this->extractText($html);

            $article->update([
                'content' => $text,
                'content_type' => $contentType,
                'fetch_status' => 'success',
                'fetch_error' => null,
                'fetch_duration_ms' => $durationMs,
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $durationMs = (int) ((microtime(true) - $startTime) * 1000);

            $article->update([
                'fetch_status' => 'timeout',
                'fetch_error' => $e->getMessage(),
                'fetch_duration_ms' => $durationMs,
            ]);
        } catch (\Throwable $e) {
            $durationMs = (int) ((microtime(true) - $startTime) * 1000);

            $article->update([
                'fetch_status' => 'failed',
                'fetch_error' => substr($e->getMessage(), 0, 1000),
                'fetch_duration_ms' => $durationMs,
            ]);
        }
    }

    private function extractText(string $html): string
    {
        // Remove script and style blocks
        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $html);

        // Remove HTML tags
        $text = strip_tags($html);

        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Normalize whitespace
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }
}
