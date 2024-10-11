<?php

namespace App\Repositories;

use App\Models\Article;
use Carbon\Carbon;

class CategoryArticleRepository implements CategoryArticleRepositoryInterface
{

    public function getCategoryArticles($categoryId, $offset, $limit)
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

    public function getArticlesByCategoryId($categoryId)
    {
        return Article::where('category_id', $categoryId)
            ->orderBy('published_at', 'desc')
            ->take(10)
            ->get(['id', 'title', 'description', 'author', 'source', 'UrlToImage', 'published_at', 'url'])
            ->map(function ($article) {
                return array_merge($article->toArray(), [
                    'published_at_human' => Carbon::parse($article->published_at)->diffForHumans(),
                ]);
            });
    }

    public function getLatestArticlesBySubCategories($subCategoryIds)
    {
        return Article::whereIn('category_id', $subCategoryIds)
            ->with('category')
            ->orderBy('published_at', 'desc')
            ->take(10)
            ->get(['id', 'title', 'description', 'author', 'source', 'UrlToImage', 'published_at', 'url', 'category_id'])
            ->map(function ($article) {
                $publishedAtHuman = Carbon::parse($article->published_at)->diffForHumans();
                return array_merge($article->toArray(), [
                    'category' => $article->category ? $article->category->name : null,
                    'published_at_human' => $publishedAtHuman,
                ]);
            });
    }
}
