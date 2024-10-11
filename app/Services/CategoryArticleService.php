<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Article;
use App\Repositories\ArticleRepositoryInterface;
use App\Repositories\CategoryArticleRepositoryInterface;
use App\Repositories\CategoryRepositoryInterface;

class CategoryArticleService implements CategoryArticleServiceInterface
{
    protected $categoryArticleRepository;
    protected $categoryRepository;
    protected $articleRepository;

    public function __construct(
        CategoryArticleRepositoryInterface $categoryArticleRepository,
        CategoryRepositoryInterface $categoryRepository,
        ArticleRepositoryInterface $articleRepository,
        )
    {
        $this->articleRepository = $articleRepository;
        $this->categoryArticleRepository = $categoryArticleRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function getCategoryArticles(Request $request, $categoryId = null)
    {
        $articlesPerPage = 10;
        $page = $request->query('page', 1);
        $offset = ($page - 1) * $articlesPerPage;

        $totalArticles = $categoryId ? $this->categoryArticleRepository->countArticlesByCategory($categoryId) : Article::count();
        
        if($categoryId){

            $articles = $this->categoryArticleRepository->getCategoryArticles($categoryId, $offset, $articlesPerPage);
        }else{
            $articles = $this->articleRepository->getArticles($offset, $articlesPerPage);
        }

        return [
            'page' => $page,
            'articles' => $articles,
            'has_more' => ($offset + $articles->count()) < $totalArticles,
        ];
    }

    public function getSingleHomeArticle()
    {
        $entertainmentCategory = Category::where('name', 'Entertainment')->whereNull('parent_id')->first();

        if (!$entertainmentCategory) {
            return response()->json(['message' => 'Entertainment category not found'], 404);
        }

        $firstSubCategory = $entertainmentCategory->subcategories()->first();

        if (!$firstSubCategory) {
            return response()->json(['message' => 'No subcategories found under Entertainment'], 404);
        }

        $latestArticle = $this->categoryArticleRepository->getFirstArticleByCategoryId($firstSubCategory->id);

        if (!$latestArticle) {
            return response()->json(['message' => 'No articles found for the subcategory'], 404);
        }

        $latestArticle->published_at_human = Carbon::parse($latestArticle->published_at)->diffForHumans();

        return response()->json([
            'article' => $latestArticle,
            'category' => $latestArticle->category ? $latestArticle->category->name : null,
        ]);
    }



    public function getSubCategoriesArticles($parentId)
    {
        $subCategories = $this->categoryRepository->getSubCategoriesByParentId($parentId);
        $allSubCategoriesData = [];

        foreach ($subCategories as $subCategory) {
            $articles = $this->categoryArticleRepository->getArticlesByCategoryId($subCategory->id);

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
            $latestArticles = $this->categoryArticleRepository->getLatestArticlesBySubCategories($subCategoryIds);

            $response[] = [
                'parent_id' => $parentCategory->id,
                'parent_category' => $parentCategory->name,
                'articles' => $latestArticles,
            ];
        }

        return response()->json($response);
    }
}
