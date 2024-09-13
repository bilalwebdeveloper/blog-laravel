<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\SearchHistoryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;

// Public Categories Access

Route::get('menu/{id}', [CategoryController::class, 'CategoryMenu']);
Route::get('sub-categories-with-articles/{id}', [CategoryController::class, 'getCategoriesWithArticlesByParent']);
Route::apiResource('categories', CategoryController::class);

// Route::get('categories/{id}', [CategoryController::class, 'show']);

// Article and Search 
Route::get('fetch-articles-from-api', [ArticleController::class, 'fetchArticlesFromApi']);
Route::get('articles/search', [ArticleController::class, 'searchArticles']);
Route::get('articles/{categoryId?}', [ArticleController::class, 'index']);
Route::get('articles/{article}', [ArticleController::class, 'showWithDetails']);

// Home Controller
Route::get('/categories-with-articles', [HomeController::class, 'getParentCategoriesWithArticles']);
Route::get('/full-article', [HomeController::class, 'fullArticle']);


// Public Authentication Routes
Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('password/code', [ResetPasswordController::class, 'sendResetCode']);
    Route::post('password/verify', [ResetPasswordController::class, 'verifyCode']);
    Route::post('password/reset', [ResetPasswordController::class, 'passwordReset']);
});

// Protected Routes requiring Sanctum Authentication
Route::middleware('auth-sanctum')->group(function () {
    // Logout
    Route::post('auth/logout', [LoginController::class, 'logout']);

    // Articles
    Route::apiResource('articles', ArticleController::class)->except(['index', 'show']);

    // Comments
    Route::post('articles/{article_id}/comments', [CommentController::class, 'store']);
    Route::delete('comments/{id}', [CommentController::class, 'destroy']);

    // Likes
    Route::post('articles/{article_id}/likes', [LikeController::class, 'store']);
    Route::delete('articles/{article_id}/likes', [LikeController::class, 'destroy']);

    // User Preferences
    Route::apiResource('user/preferences', UserPreferenceController::class);

    
    // Search History
    Route::apiResource('search/history', SearchHistoryController::class);
});



