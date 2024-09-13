<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Article;
use Carbon\Carbon; // Make sure to import Carbon

class HomeController extends Controller
{
    public function getParentCategoriesWithArticles()
    {
        // Fetch all parent categories (where parent_id is null or 0)
        $parentCategories = Category::whereNull('parent_id')->orWhere('parent_id', 0)->get();

        // Prepare the response array
        $response = [];

        // Loop through each parent category
        foreach ($parentCategories as $parentCategory) {
            // Fetch subcategories IDs for each parent category
            $subCategoryIds = $parentCategory->subcategories->pluck('id');

            // Fetch the 10 latest articles where category_id is in subcategory IDs
            $latestArticles = Article::whereIn('category_id', $subCategoryIds)
                ->with('category') // Eager load the category relationship
                ->orderBy('published_at', 'desc')
                ->take(10)
                ->get(['id', 'title', 'description', 'author', 'source', 'UrlToImage', 'published_at', 'url', 'category_id'])
                ->map(function($article) {
                    // Convert the published_at field to "time ago"
                    $publishedAtHuman = Carbon::parse($article->published_at)->diffForHumans();

                    // Explicitly add category data and human-readable time difference to each article
                    return array_merge($article->toArray(), [
                        'category' => $article->category ? $article->category->name : null,
                        'published_at_human' => $publishedAtHuman, // Add this new field
                    ]);
                });

            // Add the parent category and its articles to the response
            $response[] = [
                'parent_category' => $parentCategory->name,
                'articles' => $latestArticles
            ];
        }

        // Return the JSON response
        return response()->json($response);
    }
    public function fullArticle()
    {
        // Step 1: Fetch the "Entertainment" parent category
        $entertainmentCategory = Category::where('name', 'Entertainment')->whereNull('parent_id')->first();

        // Check if the parent category exists
        if (!$entertainmentCategory) {
            return response()->json(['message' => 'Entertainment category not found'], 404);
        }

        // Step 2: Get the first subcategory of "Entertainment"
        $firstSubCategory = $entertainmentCategory->subcategories()->first();

        // Check if the subcategory exists
        if (!$firstSubCategory) {
            return response()->json(['message' => 'No subcategories found under Entertainment'], 404);
        }

        // Step 3: Fetch the latest article from the first subcategory
        $latestArticle = Article::where('category_id', $firstSubCategory->id)
            ->orderBy('published_at', 'desc')
            ->with('category') // Eager load the category relationship
            ->first(['id', 'title', 'description', 'author', 'source', 'UrlToImage', 'published_at', 'url', 'category_id']);

        // Check if the article exists
        if (!$latestArticle) {
            return response()->json(['message' => 'No articles found for the subcategory'], 404);
        }

        // Step 4: Add the human-readable "time ago" format to the article
        $latestArticle->published_at_human = Carbon::parse($latestArticle->published_at)->diffForHumans();

        // Step 5: Return the article with related category
        return response()->json([
            'article' => $latestArticle,
            'category' => $latestArticle->category ? $latestArticle->category->name : null
        ]);
    }
}
