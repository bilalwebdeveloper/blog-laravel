<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CategoryArticleServiceInterface;


class CategoriesArticleController extends Controller
{
    protected $categoryarticleService;

    public function __construct(CategoryArticleServiceInterface $categoryarticleService)
    {
        $this->categoryarticleService = $categoryarticleService;
    }
    public function CategoriesArticles(Request $request, $categoryId = null)
    {
        try {
            $articlesData = $this->categoryarticleService->getCategoryArticles($request, $categoryId);
            return response()->json($articlesData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch subcategory articles.'.$e->getMessage()], 500);
        }
    }
    public function SingleHomeArticle()
    {
        try {
            $data = $this->categoryarticleService->getSingleHomeArticle();
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch Home Article.'], 500);
        }
    }
    public function getSubCategoriesArticles($parent_id)
    {
        $data = $this->categoryarticleService->getSubCategoriesArticles($parent_id);
        return response()->json($data, 200);
    }
    public function getCategoriesArticles()
    {
        $data = $this->categoryarticleService->getCategoriesArticles();
        return response()->json($data, 200);
    }
}
