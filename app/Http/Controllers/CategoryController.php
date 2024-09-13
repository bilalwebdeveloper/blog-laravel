<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Article;
use Carbon\Carbon; 

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::all(), 200);
    }

    public function show($id)
    {
        return response()->json(Category::findOrFail($id), 200);
    }

    public function store(Request $request)
    {
        $category = Category::create($request->all());
        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->all());
        return response()->json($category, 200);
    }

    public function destroy($id)
    {
        Category::destroy($id);
        return response()->json(null, 204);
    }
    /**
     * Fetch the specified number of parent categories along with their subcategories.
     *
     * @param int $id Number of parent categories to fetch.
     * @return \Illuminate\Http\JsonResponse
     */
    public function CategoryMenu($id)
    {
        // Fetch the specified number of parent categories (where parent_id is null or 0)
        $parentCategories = Category::whereNull('parent_id')
            ->orWhere('parent_id', 0)
            ->take($id)
            ->get();

        // Loop through each parent category to get their subcategories
        $response = $parentCategories->map(function ($parentCategory) {
            return [
                'parent_category_id' => $parentCategory->id,
                'parent_category' => $parentCategory->name,
                'subcategories' => $parentCategory->subcategories->map(function ($subcategory) {
                    return [
                        'id' => $subcategory->id,
                        'name' => $subcategory->name,
                    ];
                }),
            ];
        });

        // Return the response in JSON format
        return response()->json($response);
    }
    public function getCategoriesWithArticlesByParent($parent_id)
    {
        // Fetch subcategories where parent_id matches the passed parent_id
        $subCategories = Category::where('parent_id', $parent_id)->get();

        // Prepare an array to store articles and subcategory data
        $allSubCategoriesData = [];

        // Loop through each subcategory
        foreach ($subCategories as $subCategory) {
            // Fetch the latest 10 articles for each subcategory
            $articles = Article::where('category_id', $subCategory->id)
                ->orderBy('published_at', 'desc')
                ->take(10) // Limit to 10 articles per subcategory
                ->get(['id', 'title', 'description', 'author', 'source', 'UrlToImage', 'published_at', 'url', 'category_id'])
                ->map(function($article) {
                    // Format the published_at field to "time ago"
                    $publishedAtHuman = Carbon::parse($article->published_at)->diffForHumans();

                    // Return the article data along with the formatted published_at field
                    return array_merge($article->toArray(), [
                        'published_at_human' => $publishedAtHuman, // Add human-readable time difference
                    ]);
                });

            // Add the subcategory name and its articles to the response
            $allSubCategoriesData[] = [
                'subcategory_name' => $subCategory->name, // Add subcategory name
                'subcategory_id' => $subCategory->id, // Add subcategory id
                'articles' => $articles
            ];
        }

        // Return the response as JSON
        return response()->json([
            'parent_category_id' => $parent_id,
            'subcategories' => $allSubCategoriesData,
        ]);
    }



}
