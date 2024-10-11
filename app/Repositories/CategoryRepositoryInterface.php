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
    public function getParentCategories($limit=null);
    public function getCategoriesByIds(array $categoryIds, $limit);
}
