<?php
namespace App\Services\NewsApi;

use App\Contracts\NewsApiInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GuardianApiService implements NewsApiInterface
{
    private string $apiKey;
    private string $baseUrl = 'https://content.guardianapis.com';

    public function __construct()
    {
        $this->apiKey = config('services.guardian.key');
    }

    public function fetchArticles(array $parameters = []): array
    {
        $cacheKey = 'guardian_' . md5(json_encode($parameters));

        return Cache::remember($cacheKey, 3600, function () use ($parameters) {
            $response = Http::get($this->baseUrl . '/search', array_merge([
                'api-key' => $this->apiKey,
                'show-fields' => 'headline,thumbnail,bodyText,byline',
                'page-size' => 50,
            ], $parameters));

            if (!$response->successful()) {
                return [];
            }

            $results = $response->json()['response']['results'] ?? [];
            
            return array_map(function ($article) {
                return [
                    'title' => $article['fields']['headline'] ?? '',
                    'content' => $article['fields']['bodyText'] ?? '',
                    'url' => $article['webUrl'] ?? '',
                    'urlToImage' => $article['fields']['thumbnail'] ?? null,
                    'author' => $article['fields']['byline'] ?? '',
                    'publishedAt' => $article['webPublicationDate'] ?? '',
                ];
            }, $results);
        });
    }

    public function getSourceName(): string
    {
        return 'The Guardian';
    }

    public function getCategories(): array
    {
        return [
            'world',
            'politics',
            'business',
            'technology',
            'sport',
            'culture',
            'lifestyle'
        ];
    }
} 