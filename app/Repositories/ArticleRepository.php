<?php

namespace App\Repositories;

use App\Models\Article;
use Carbon\Carbon;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function findArticleById($id)
    {
        return Article::withCount('likes')->with('comments')->findOrFail($id);
    }

    public function createArticle(array $data)
    {
        return Article::create($data);
    }

    public function updateArticle($id, array $data)
    {
        $article = Article::findOrFail($id);
        $article->update($data);
        return $article;
    }

    public function deleteArticle($id)
    {
        return Article::destroy($id);
    }

    public function searchArticles(string $query, ?string $date = null, ?string $source = null, ?string $category = null)
    {
        $articles = Article::where(function ($q) use ($query) {
            $q->where('title', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%");
        });
    
        // Apply the date filter if provided
        if ($date) {
            $articles->whereDate('published_at', $date);
        }
    
        // Apply the source filter if provided
        if ($source) {
            $articles->where('source', $source);
        }
    
        // Apply the category filter if provided
        if ($category) {
            $articles->where('category_id', $category);
        }
    
        return $articles->get(['id', 'title', 'description', 'source', 'category_id', 'published_at' ,'UrlToImage']);
    }
    
    public function findArticleByUrl($url)
    {
        return Article::where('url', $url)->first();
    }

    public function getSubcategoryArticles($categoryId, $offset, $limit)
    {
        return Article::where('category_id', $categoryId)
            ->orderBy('published_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get(['id', 'title', 'description', 'author', 'source', 'UrlToImage', 'published_at', 'url', 'category_id'])
            ->map(function ($article) {
                return array_merge($article->toArray(), [
                    'published_at_human' => Carbon::parse($article->published_at)->diffForHumans(),
                ]);
            });
    }

    public function getArticles($offset, $limit)
    {
        return Article::orderBy('published_at', 'desc')
                ->offset($offset)
                ->limit($limit)
                ->get(['id', 'title', 'description', 'author', 'source', 'UrlToImage', 'published_at', 'url', 'category_id'])
                ->map(function ($article) {
                    return array_merge($article->toArray(), [
                        'published_at_human' => Carbon::parse($article->published_at)->diffForHumans(),
                    ]);
                });
    }

    public function countArticlesByCategory($categoryId)
    {
        return Article::where('category_id', $categoryId)->count();
    }

    public function getFirstArticleByCategoryId($categoryId)
    {
        return Article::where('category_id', $categoryId)
            ->orderBy('published_at', 'desc')
            ->with('category')
            ->first(['id', 'title', 'description', 'author', 'source', 'UrlToImage', 'published_at', 'url', 'category_id']);
    }
    
    public function fetchAllSource()
    {
        return Article::distinct()
            ->orderBy('source')
            ->pluck('source');
    }
    
    public function fetchAllAuthors()
    {
        return Article::distinct()
            ->orderBy('author')
            ->pluck('author');
    }
}
