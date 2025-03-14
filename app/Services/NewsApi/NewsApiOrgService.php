<?php
namespace App\Services\NewsApi;

use App\Contracts\NewsApiInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NewsApiOrgService implements NewsApiInterface
{
    private string $apiKey;
    private string $baseUrl = 'https://newsapi.org/v2';

    public function __construct()
    {
        $this->apiKey = config('services.newsapi.key');
    }

    public function fetchArticles(array $parameters = []): array
    {
        $cacheKey = 'newsapi_' . md5(json_encode($parameters));

        return Cache::remember($cacheKey, 3600, function () use ($parameters) {
            $response = Http::get($this->baseUrl . '/top-headlines', array_merge([
                'apiKey' => $this->apiKey,
                'language' => 'en',
            ], $parameters));

            //echo '<pre>'; print_r($response->json()); echo '</pre>'; die('here');

            if (!$response->successful()) {
                return [];
            }

            return $response->json()['articles'] ?? [];
        });
    }

    public function getSourceName(): string
    {
        return 'NewsAPI.org';
    }

    public function getCategories(): array
    {
        return [
            'business',
            'entertainment',
            'general',
            'health',
            'science',
            'sports',
            'technology'
        ];
    }
} 