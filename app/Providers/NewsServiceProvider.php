<?php

namespace App\Providers;

use App\Contracts\NewsApiInterface;
use App\Services\NewsApi\NewsApiOrgService;
use App\Services\NewsApi\GuardianApiService;
use App\Services\NewsApi\NytApiService;
use Illuminate\Support\ServiceProvider;

class NewsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->tag([
            NewsApiOrgService::class,
            GuardianApiService::class,
            NytApiService::class,
        ], 'news_apis');

        // Register the first service as the default
        $this->app->bind(NewsApiInterface::class, NewsApiOrgService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
