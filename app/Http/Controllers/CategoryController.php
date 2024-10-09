<?php
// app/Http/Controllers/CategoryController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CategoryServiceInterface;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->getAllCategories();
        return response()->json($categories, 200);
    }

    public function show($id)
    {
        $category = $this->categoryService->getCategoryById($id);
        return response()->json($category, 200);
    }

    public function store(Request $request)
    {
        $category = $this->categoryService->createCategory($request->all());
        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = $this->categoryService->updateCategory($id, $request->all());
        return response()->json($category, 200);
    }

    public function destroy($id)
    {
        $this->categoryService->deleteCategory($id);
        return response()->json(null, 204);
    }

    public function getSubCategoriesArticles($parent_id)
    {
        $data = $this->categoryService->getSubCategoriesArticles($parent_id);
        return response()->json($data, 200);
    }
    public function getCategoriesArticles()
    {
        $data = $this->categoryService->getCategoriesArticles();
        return response()->json($data, 200);
    }
}
