<?php
// app/Services/CategoryServiceInterface.php

namespace App\Services;

use Illuminate\Http\Request;

interface CategoryServiceInterface
{
    public function getAllCategories();
    public function getCategoryById($id);
    public function createCategory(array $data);
    public function updateCategory($id, array $data);
    public function deleteCategory($id);
    public function getSubCategoriesArticles($parentId);
    public function getCategoriesArticles();
}
