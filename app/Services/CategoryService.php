<?php
// app/Services/CategoryService.php

namespace App\Services;

use App\Repositories\CategoryRepositoryInterface;

class CategoryService implements CategoryServiceInterface
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories()
    {
        return $this->categoryRepository->getAllCategories();
    }

    public function getCategoryById($id)
    {
        return $this->categoryRepository->getCategoryById($id);
    }

    public function createCategory(array $data)
    {
        return $this->categoryRepository->createCategory($data);
    }

    public function updateCategory($id, array $data)
    {
        return $this->categoryRepository->updateCategory($id, $data);
    }

    public function deleteCategory($id)
    {
        return $this->categoryRepository->deleteCategory($id);
    }

    public function getSubCategoriesArticles($parentId)
    {
        $subCategories = $this->categoryRepository->getSubCategoriesByParentId($parentId);
        $allSubCategoriesData = [];

        foreach ($subCategories as $subCategory) {
            $articles = $this->categoryRepository->getArticlesByCategoryId($subCategory->id);

            $allSubCategoriesData[] = [
                'subcategory_name' => $subCategory->name,
                'subcategory_id' => $subCategory->id,
                'articles' => $articles,
            ];
        }

        return [
            'parent_category_id' => $parentId,
            'subcategories' => $allSubCategoriesData,
        ];
    }

    public function getCategoriesArticles()
    {
        $parentCategories = $this->categoryRepository->getParentCategories();
        $response = [];

        foreach ($parentCategories as $parentCategory) {
            $subCategoryIds = $parentCategory->subcategories->pluck('id');
            $latestArticles = $this->categoryRepository->getLatestArticlesBySubCategories($subCategoryIds);

            $response[] = [
                'parent_id' => $parentCategory->id,
                'parent_category' => $parentCategory->name,
                'articles' => $latestArticles,
            ];
        }

        return response()->json($response);
    }
}
