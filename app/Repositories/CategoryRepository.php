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
