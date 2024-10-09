<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Article;
use Carbon\Carbon;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllCategories()
    {
        return Category::all();
    }

    public function getCategoryById($id)
    {
        return Category::findOrFail($id);
    }

    public function createCategory(array $data)
    {
        return Category::create($data);
    }

    public function updateCategory($id, array $data)
    {
        $category = Category::findOrFail($id);
        $category->update($data);
        return $category;
    }

    public function deleteCategory($id)
    {
        return Category::destroy($id);
    }

    public function getSubCategoriesByParentId($parentId)
    {
        return Category::where('parent_id', $parentId)->get();
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
    public function getParentCategories($limit = null)
    {
        $query = Category::whereNull('parent_id')
            ->orWhere('parent_id', 0);

        if ($limit) {
            $query->take($limit);
        }

        return $query->get();
    }


    public function getCategoriesByIds(array $categoryIds, $limit)
    {
        return Category::whereIn('id', $categoryIds)
            ->take($limit)
            ->get();
    }
}
