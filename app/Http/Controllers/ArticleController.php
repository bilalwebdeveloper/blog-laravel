<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Like;
use App\Models\Comment;
use App\Services\NewsService;
use Carbon\Carbon;
use App\Models\Category;

class ArticleController extends Controller
{
    protected $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }
    public function searchArticles(Request $request)
    {
        $query = $request->input('query');
        
        // Validate the input
        $request->validate([
            'query' => 'required|string|min:1',
        ]);

        $results = Article::where('title', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->get(['id', 'title', 'description']);

        // Return the results as JSON
        return response()->json($results);
    }
    public function fetchArticlesFromApi(Request $request)
    {
        // Fetch all subcategories from the database
        $subcategories = Category::whereNotNull('parent_id')->get();

        foreach ($subcategories as $subcategory) {
            $keyword = $subcategory->name;

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
                    $publishedAt = isset($article['publishedAt']) ? Carbon::parse($article['publishedAt'])->format('Y-m-d H:i:s') : Carbon::parse(now())->format('Y-m-d H:i:s');

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
                }
            }
        }

        return response()->json(['message' => 'Articles saved successfully!'], 200);
    }



    public function index(Request $request, $categoryId = null)
{
    $articlesPerPage = 10;

    // Get the page number from the request query string, default to 1 if not present
    $page = $request->query('page', 1); // Default to page 1
    $offset = ($page - 1) * $articlesPerPage;

    // Check if category ID is provided
    if ($categoryId) {
        // Fetch articles for a specific category
        $query = Article::where('category_id', $categoryId);
    } else {
        // Fetch all articles
        $query = Article::query();
    }

    // 1. First, get the total number of articles (without pagination)
    $totalArticles = $query->count();

    // 2. Then, apply pagination (limit and offset)
    $articles = $query->orderBy('published_at', 'desc')
        ->offset($offset)
        ->limit($articlesPerPage)
        ->get(['id', 'title', 'description', 'author', 'source', 'UrlToImage', 'published_at', 'url', 'category_id'])
        ->map(function ($article) {
            $publishedAtHuman = Carbon::parse($article->published_at)->diffForHumans();
            return array_merge($article->toArray(), [
                'published_at_human' => $publishedAtHuman,
            ]);
        });

    // 3. Calculate if there's more data to load
    $hasMore = ($offset + $articles->count()) < $totalArticles;

    return response()->json([
        'page' => $page,
        'articles' => $articles,
        'has_more' => $hasMore, // Whether more articles are available
    ]);
}





    public function show($id)
    {
        return response()->json(Article::findOrFail($id), 200);
    }

    public function showWithDetails($id)
    {
        $article = Article::withCount('likes')->with('comments')->findOrFail($id);
        return response()->json($article, 200);
    }

    public function store(Request $request)
    {
        $article = Article::create($request->all());
        return response()->json($article, 201);
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $article->update($request->all());
        return response()->json($article, 200);
    }

    public function destroy($id)
    {
        Article::destroy($id);
        return response()->json(null, 204);
    }
}
