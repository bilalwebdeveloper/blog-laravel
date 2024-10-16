<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\SearchHistoryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CategoriesArticleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;


// Public Categories Access
Route::apiResource('categories', CategoryController::class);

Route::get('categories-with-articles', [CategoriesArticleController::class, 'getCategoriesArticles']);
Route::get('sub-categories-with-articles/{id}', [CategoriesArticleController::class, 'getSubCategoriesArticles']);
Route::get('articles/sub-categories/{categoryId?}', [CategoriesArticleController::class, 'CategoriesArticles']);
Route::get('single-home-article', [CategoriesArticleController::class, 'SingleHomeArticle']);

// Article and Search 
Route::post('articles/search', [ArticleController::class, 'searchArticles']);
Route::get('/article/source', [ArticleController::class, 'fetchAllSource']); 
Route::get('/articles/authors', [ArticleController::class, 'fetchAllAuthors']); 
Route::apiResource('articles', ArticleController::class);

// Fetch header menu
Route::get('menu/header', [MenuController::class, 'getHeaderMenu']);
// Fetch footer menu
Route::get('menu/footer', [MenuController::class, 'getFooterMenu']);

// Public Authentication Routes
Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('password/code', [ResetPasswordController::class, 'sendResetCode']);
    Route::post('password/verify', [ResetPasswordController::class, 'verifyCode']);
    Route::post('password/reset', [ResetPasswordController::class, 'passwordReset']);
});

// Protected Routes requiring Sanctum Authentication
Route::middleware('auth:sanctum')->group(function () {
    // Logout
    Route::get('auth/logout', [AuthController::class, 'logout']);

    //Profile
    Route::get('/user/profile', [ProfileController::class, 'show']);
    Route::post('/user/profile', [ProfileController::class, 'update']);

    // User Preferences
    Route::apiResource('preferences', UserPreferenceController::class);

    // Search History
    Route::apiResource('search/history', SearchHistoryController::class);

    // Fetch header menu
    Route::get('/menu/header/{$user_id}', [MenuController::class, 'getHeaderMenu']);
    // Fetch footer menu
    Route::get('/menu/footer/{$user_id}', [MenuController::class, 'getFooterMenu']);
    
    
});
