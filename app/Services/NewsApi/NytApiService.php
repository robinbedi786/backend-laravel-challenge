<?php
namespace App\Services\NewsApi;

use App\Contracts\NewsApiInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NytApiService implements NewsApiInterface
{
    private string $apiKey;
    private string $baseUrl = 'https://api.nytimes.com/svc';

    public function __construct()
    {
        $this->apiKey = config('services.nyt.key');
    }

    public function fetchArticles(array $parameters = []): array
    {
        $cacheKey = 'nyt_' . md5(json_encode($parameters));

        return Cache::remember($cacheKey, 3600, function () use ($parameters) {
            $response = Http::get($this->baseUrl . '/news/v3/content/all/all.json', array_merge([
                'api-key' => $this->apiKey,
                'limit' => 50,
            ], $parameters));

            if (!$response->successful()) {
                return [];
            }

            $results = $response->json()['results'] ?? [];
            
            return array_map(function ($article) {
                return [
                    'title' => $article['title'] ?? '',
                    'content' => $article['abstract'] ?? '',
                    'url' => $article['url'] ?? '',
                    'urlToImage' => $article['multimedia'][0]['url'] ?? null,
                    'author' => $article['byline'] ?? '',
                    'publishedAt' => $article['published_date'] ?? '',
                ];
            }, $results);
        });
    }

    public function getSourceName(): string
    {
        return 'New York Times';
    }

    public function getCategories(): array
    {
        return [
            'arts',
            'automobiles',
            'books',
            'business',
            'fashion',
            'food',
            'health',
            'home',
            'insider',
            'magazine',
            'movies',
            'politics',
            'science',
            'sports',
            'technology',
            'theater',
            'travel',
            'world'
        ];
    }
} 