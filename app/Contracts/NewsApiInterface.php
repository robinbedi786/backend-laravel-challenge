<?php
namespace App\Contracts;

interface NewsApiInterface
{
    /**
     * Fetch articles from the news API
     *
     * @param array $parameters
     * @return array
     */
    public function fetchArticles(array $parameters = []): array;

    /**
     * Get the source name
     *
     * @return string
     */
    public function getSourceName(): string;

    /**
     * Get available categories
     *
     * @return array
     */
    public function getCategories(): array;
} 