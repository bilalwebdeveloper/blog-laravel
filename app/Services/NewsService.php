<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\Article;
use Carbon\Carbon;
use App\Models\Category;
use App\Repositories\ArticleRepositoryInterface;
use Illuminate\Support\Facades\Log;


class NewsService
{
    protected $articleRepository;
    protected $client;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->client = new Client();
        $this->articleRepository = $articleRepository;
    }

    public function getNewsFromOpenNews($keyword)
    {
        $apiKey = env('OPENNEWS_API_KEY');
        $url = "https://opennewsapi.com/v1/news?query={$keyword}&apiKey={$apiKey}";

        $response = $this->client->get($url);
        return json_decode($response->getBody(), true);
    }

    public function getNewsFromNewsCred($keyword)
    {
        $apiKey = env('NEWSCRED_API_KEY');
        $url = "https://newscredapi.com/v1/articles?query={$keyword}&apiKey={$apiKey}";

        $response = $this->client->get($url);
        return json_decode($response->getBody(), true);
    }
    public function fetchArticlesFromNewsData()
    {
        // Retrieve all subcategories that have a parent category.
        $subcategories = Category::whereNotNull('parent_id')->get();
        $allArticles = [];

        // Loop through each subcategory to get related news articles.
        foreach ($subcategories as $subcategory) {
            $keyword = $subcategory->name;
            $newsDataArticles = $this->getNewsFromNewsData($keyword);
            Log::info($newsDataArticles);

            // Check if the API response contains articles.
            if (isset($newsDataArticles['results'])) {
                foreach ($newsDataArticles['results'] as $article) {
                    // Check if an article with the same URL already exists in the database.
                    $existingArticle = $this->articleRepository->findArticleByUrl($article['link']);

                    // If no existing article is found, create a new one.
                    if (!$existingArticle && !empty($article['title'])) {
                        $publishedAt = isset($article['pubDate']) ? Carbon::parse($article['pubDate'])->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s');

                        // Create a new article using the repository.
                        $allArticles[] = $this->articleRepository->createArticle([
                            'title' => $article['title'] ?? 'Untitled',
                            'description' => $article['content'] ?? '',
                            'author' => !empty($article['creator']) ? $article['creator'][0] : 'Unknown', // Handle possible array of creators.
                            'source' => $article['source_name'] ?? 'Unknown', // Source name from Newsdata API.
                            'category_id' => $subcategory->id, // Assign category from subcategory.
                            'published_at' => $publishedAt,
                            'UrlToImage' => $article['image_url'] ?? '', // Image URL from Newsdata API.
                            'url' => $article['link'], // Article link.
                        ]);

                        Log::info("Article saved: {$article['title']}");
                    }
                }
            } else {
                Log::info('No articles found for keyword: ' . $keyword);
            }
        }

        return $allArticles;
    }
    public function getNewsFromNewsData($keyword)
    {
        $apiKey = config('services.newsdata.news_data_api_key');
        $url = "https://newsdata.io/api/1/news?apikey={$apiKey}&q={$keyword}";

        try {
            $response = $this->client->get($url);
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            // Log the error or handle the exception as needed.
            Log::error('Error fetching news data: ' . $e->getMessage());
            return [
                'error' => 'Unable to fetch news data.',
                'message' => $e->getMessage()
            ];
        }
    }

    public function fetchArticlesFromNewsApi()
    {
        // Retrieve all subcategories that have a parent category.
        $subcategories = Category::whereNotNull('parent_id')->get();
        $allArticles = [];

        // Loop through each subcategory to get related news articles.
        foreach ($subcategories as $subcategory) {
            $keyword = $subcategory->name;
            $newsDataArticles = $this->getNewsFromNewsApi($keyword);
            Log::info($newsDataArticles);

            // Check if the API response contains articles.
            if (isset($newsDataArticles['results'])) {
                foreach ($newsDataArticles['results'] as $article) {
                    // Check if an article with the same URL already exists in the database.
                    $existingArticle = $this->articleRepository->findArticleByUrl($article['link']);

                    // If no existing article is found, create a new one.
                    if (!$existingArticle && !empty($article['title'])) {
                        $publishedAt = isset($article['pubDate']) ? Carbon::parse($article['pubDate'])->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s');

                        // Create a new article using the repository.
                        $allArticles[] = $this->articleRepository->createArticle([
                            'title' => $article['title'] ?? 'Untitled',
                            'description' => $article['content'] ?? '',
                            'author' => !empty($article['creator']) ? $article['creator'][0] : 'Unknown', // Handle possible array of creators.
                            'source' => $article['source_name'] ?? 'Unknown', // Source name from Newsdata API.
                            'category_id' => $subcategory->id, // Assign category from subcategory.
                            'published_at' => $publishedAt,
                            'UrlToImage' => $article['image_url'] ?? '', // Image URL from Newsdata API.
                            'url' => $article['link'], // Article link.
                        ]);

                        Log::info("Article saved: {$article['title']}");
                    }
                }
            } else {
                Log::info('No articles found for keyword: ' . $keyword);
            }
        }

        return $allArticles;
    }
    public function getNewsFromNewsApi($keyword)
    {
        $apiKey = config('services.newsdata.news_api_key');
        $url = "https://newsapi.org/v2/everything?q={$keyword}&apiKey={$apiKey}";
        try {
            $response = $this->client->get($url);
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            // Log the error or handle the exception as needed.
            Log::error('Error fetching news data: ' . $e->getMessage());
            return [
                'error' => 'Unable to fetch news data.',
                'message' => $e->getMessage()
            ];
        }
    }
    
}
