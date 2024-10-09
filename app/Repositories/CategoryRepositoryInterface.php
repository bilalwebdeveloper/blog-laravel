<?php
namespace App\Repositories;

interface CategoryRepositoryInterface
{
    public function getAllCategories();
    public function getCategoryById($id);
    public function createCategory(array $data);
    public function updateCategory($id, array $data);
    public function deleteCategory($id);
    public function getSubCategoriesByParentId($parentId);
    public function getArticlesByCategoryId($categoryId);
    public function getParentCategories();
    public function getLatestArticlesBySubCategories($subCategoryIds);
    public function getCategoriesByIds(array $categoryIds, $limit);
}
