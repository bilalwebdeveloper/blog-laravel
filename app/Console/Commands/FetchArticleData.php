<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use App\Models\Article;
use Carbon\Carbon;
use App\Services\NewsService;

class FetchArticleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:article-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This cron job will get the latest news from API and store it in our database';

    /**
     * The NewsService instance.
     *
     * @var \App\Services\NewsService
     */
    protected $newsService;

    /**
     * Create a new command instance.
     *
     * @param \App\Services\NewsService $newsService
     * @return void
     */
    public function __construct(NewsService $newsService)
    {
        parent::__construct();
        $this->newsService = $newsService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Starting to fetch articles from API.');

        // Fetch all subcategories from the database
        $subcategories = Category::whereNotNull('parent_id')->get();

        foreach ($subcategories as $subcategory) {
            $keyword = $subcategory->name;

            Log::info("Fetching articles for subcategory: {$keyword}");

            try {
                // Fetch articles from the News API using the subcategory name as keyword
                $newsApiArticles = $this->newsService->getNewsFromNewsAPI($keyword);
                // If using more APIs, uncomment and integrate
                // $openNewsArticles = $this->newsService->getNewsFromOpenNews($keyword);
                // $newsCredArticles = $this->newsService->getNewsFromNewsCred($keyword);

                // Merge all articles from different sources
                $articles = array_merge(
                    $newsApiArticles['articles'] ?? [],
                    $openNewsArticles['articles'] ?? [],
                    $newsCredArticles['articles'] ?? []
                );

                // Loop through the articles and store them in the database
                foreach ($articles as $article) {
                    // Check if the article with the same URL already exists
                    $existingArticle = Article::where('url', $article['url'])->first();

                    if (!$existingArticle && $article['title'] != '[Removed]') {
                        // Convert the published_at datetime to MySQL format
                        $publishedAt = isset($article['publishedAt'])
                            ? Carbon::parse($article['publishedAt'])->format('Y-m-d H:i:s')
                            : Carbon::parse(now())->format('Y-m-d H:i:s');

                        // Create a new article record
                        Article::create([
                            'title' => $article['title'] ?? 'Untitled',
                            'description' => $article['content'] ?? '',
                            'author' => $article['author'] ?? 'Unknown',
                            'source' => $article['source']['name'] ?? 'Unknown',
                            'category_id' => $subcategory->id, // Set the category_id to the subcategory's ID
                            'published_at' => $publishedAt,
                            'UrlToImage' => $article['urlToImage'] ?? '',
                            'url' => $article['url'],
                        ]);

                        Log::info("Article saved: {$article['title']}");
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error fetching articles for subcategory: ' . $keyword . '. Error: ' . $e->getMessage());
            }
        }

        Log::info('Finished fetching articles from API.');

        return 0;
    }
}
