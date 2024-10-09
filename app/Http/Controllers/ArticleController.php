<?php
// app/Http/Controllers/ArticleController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ArticleServiceInterface;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\SearchArticlesRequest;
use App\Http\Requests\UpdateArticleRequest;

class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(ArticleServiceInterface $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index($id)
    {
        try {
            $article = $this->articleService->findArticleById($id);
            return response()->json($article, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Article not found.'], 404);
        }
    }

    public function show($id)
    {
        try {
            $article = $this->articleService->findArticleById($id);
            return response()->json($article, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Article not found.'], 404);
        }
    }

    public function store(StoreArticleRequest $request)
    {
        try {
            $article = $this->articleService->createArticle($request->validated());
            return response()->json($article, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create article.'], 500);
        }
    }

    public function update(UpdateArticleRequest $request, $id)
    {
        try {
            $article = $this->articleService->updateArticle($id, $request->validated());
            return response()->json($article, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update article.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->articleService->deleteArticle($id);
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete article.'], 500);
        }
    }

    public function searchArticles(SearchArticlesRequest $request)
    {
        // Extract validated inputs
        $query = $request->input('query');
        $date = $request->input('date');
        $source = $request->input('source');
        $category = $request->input('category');

        try {
            // Pass all parameters to the articleService
            $results = $this->articleService->searchArticles($query, $date, $source, $category);
            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to search articles.'], 500);
        }
    }


    public function fetchArticlesFromApi(Request $request)
    {
        try {
            $articles = $this->articleService->fetchArticlesFromApi($request);
            return response()->json(['message' => 'Articles saved successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch articles from API.'], 500);
        }
    }

    public function subCategoriesArticles(Request $request, $categoryId = null)
    {
        try {
            $articlesData = $this->articleService->getSubcategoryArticles($request, $categoryId);
            return response()->json($articlesData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch subcategory articles.'], 500);
        }
    }
    public function SingleHomeArticle()
    {
        try {
            $data = $this->articleService->getSingleHomeArticle();
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch subcategory articles.'], 500);
        }
    }
    function fetchAllSource() {
        try {
            $data = $this->articleService->fetchAllSource();
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch subcategory articles.'], 500);
        }
    }
    function fetchAllAuthors() {
        try {
            $data = $this->articleService->fetchAllAuthors();
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch subcategory articles.'], 500);
        }
    }
    
}
