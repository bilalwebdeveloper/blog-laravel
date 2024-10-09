<?php
// app/Services/ArticleService.php

namespace App\Services;

use App\Repositories\ArticleRepositoryInterface;
use App\Models\Category;
use App\Models\Article;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ArticleService implements ArticleServiceInterface
{
    protected $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function findArticleById($id)
    {
        return $this->articleRepository->findArticleById($id);
    }

    public function createArticle(array $data)
    {
        return $this->articleRepository->createArticle($data);
    }

    public function updateArticle($id, array $data)
    {
        return $this->articleRepository->updateArticle($id, $data);
    }

    public function deleteArticle($id)
    {
        return $this->articleRepository->deleteArticle($id);
    }

    public function searchArticles(string $query, ?string $date = null, ?string $source = null, ?string $category = null)
    {
        return $this->articleRepository->searchArticles($query, $date, $source, $category);
    }

    public function fetchArticlesFromApi(Request $request)
    {
        $subcategories = Category::whereNotNull('parent_id')->get();
        $allArticles = [];

        foreach ($subcategories as $subcategory) {
            $keyword = $subcategory->name;
            $newsApiArticles = $this->getNewsFromNewsAPI($keyword);

            foreach ($newsApiArticles['articles'] as $article) {
                $existingArticle = $this->articleRepository->findArticleByUrl($article['url']);

                if (!$existingArticle && $article['title'] != '[Removed]') {
                    $publishedAt = isset($article['publishedAt']) ? Carbon::parse($article['publishedAt'])->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s');

                    $allArticles[] = $this->articleRepository->createArticle([
                        'title' => $article['title'] ?? 'Untitled',
                        'description' => $article['content'] ?? '',
                        'author' => $article['author'] ?? 'Unknown',
                        'source' => $article['source']['name'] ?? 'Unknown',
                        'category_id' => $subcategory->id,
                        'published_at' => $publishedAt,
                        'UrlToImage' => $article['urlToImage'] ?? '',
                        'url' => $article['url'],
                    ]);
                }
            }
        }

        return $allArticles;
    }

    public function getSubcategoryArticles(Request $request, $categoryId = null)
    {
        $articlesPerPage = 10;
        $page = $request->query('page', 1);
        $offset = ($page - 1) * $articlesPerPage;

        $totalArticles = $categoryId ? $this->articleRepository->countArticlesByCategory($categoryId) : Article::count();
        
        if($categoryId){

            $articles = $this->articleRepository->getSubcategoryArticles($categoryId, $offset, $articlesPerPage);
        }else{
            $articles = $this->articleRepository->getArticles($offset, $articlesPerPage);
        }

        return [
            'page' => $page,
            'articles' => $articles,
            'has_more' => ($offset + $articles->count()) < $totalArticles,
        ];
    }

    public function getSingleHomeArticle()
    {
        $entertainmentCategory = Category::where('name', 'Entertainment')->whereNull('parent_id')->first();

        if (!$entertainmentCategory) {
            return response()->json(['message' => 'Entertainment category not found'], 404);
        }

        $firstSubCategory = $entertainmentCategory->subcategories()->first();

        if (!$firstSubCategory) {
            return response()->json(['message' => 'No subcategories found under Entertainment'], 404);
        }

        $latestArticle = $this->articleRepository->getFirstArticleByCategoryId($firstSubCategory->id);

        if (!$latestArticle) {
            return response()->json(['message' => 'No articles found for the subcategory'], 404);
        }

        $latestArticle->published_at_human = Carbon::parse($latestArticle->published_at)->diffForHumans();

        return response()->json([
            'article' => $latestArticle,
            'category' => $latestArticle->category ? $latestArticle->category->name : null,
        ]);
    }

    private function getNewsFromNewsAPI($keyword)
    {
        // Logic to fetch news from News API
        return [];
    }
    function fetchAllSource() {
        return $this->articleRepository->fetchAllSource();
    }
    function fetchAllAuthors() {
        return $this->articleRepository->fetchAllAuthors();
    }
}
