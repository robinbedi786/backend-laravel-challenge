<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use App\Models\NewsSource;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class FetchNewsArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news articles from all configured sources';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $newsApis = app()->tagged('news_apis');

        foreach ($newsApis as $api) {
            $this->info("Fetching articles from {$api->getSourceName()}...");

            try {
                // Get or create the news source
                $source = NewsSource::firstOrCreate(
                    ['name' => $api->getSourceName()],
                    [
                        'is_active' => true,
                        'base_url' => '',
                        'api_endpoint' => '',
                    ]
                );

                // Fetch articles for each category
                foreach ($api->getCategories() as $categoryName) {
                    $this->info("Processing category: {$categoryName}");

                    // Get or create the category
                    $category = Category::firstOrCreate(
                        ['name' => $categoryName],
                        ['slug' => Str::slug($categoryName)]
                    );

                    // Fetch articles
                    $articles = $api->fetchArticles(['category' => $categoryName]);

                    foreach ($articles as $articleData) {
                        Article::updateOrCreate(
                            [
                                'source_url' => $articleData['url'],
                                'news_source_id' => $source->id
                            ],
                            [
                                'title' => $articleData['title'],
                                'slug' => Str::slug($articleData['title']),
                                'content' => $articleData['content'],
                                'summary' => $articleData['content'] ?? null,
                                'image_url' => $articleData['urlToImage'] ?? null,
                                'author' => $articleData['author'] ?? null,
                                'category_id' => $category->id,
                                'published_at' => $articleData['publishedAt'],
                            ]
                        );
                    }
                }

                $this->info("Successfully fetched articles from {$api->getSourceName()}");
            } catch (\Exception $e) {
                $this->error("Error fetching articles from {$api->getSourceName()}: " . $e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
