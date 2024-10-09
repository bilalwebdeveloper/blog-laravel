<?php

// app/Providers/AppServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\UserServiceInterface;
use App\Services\UserService;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\ArticleRepositoryInterface;
use App\Repositories\ArticleRepository;
use App\Services\ArticleServiceInterface;
use App\Services\ArticleService;
use App\Services\CategoryServiceInterface;
use App\Services\CategoryService;
use App\Repositories\CategoryRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Services\NewsFeedServiceInterface;
use App\Services\NewsFeedService;
use App\Repositories\NewsFeedRepositoryInterface;
use App\Repositories\NewsFeedRepository;
use App\Services\MenuServiceInterface;
use App\Services\MenuService;
class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);

        $this->app->bind(ArticleRepositoryInterface::class, ArticleRepository::class);
        $this->app->bind(ArticleServiceInterface::class, ArticleService::class);
        
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        
        $this->app->bind(MenuServiceInterface::class, MenuService::class);

        $this->app->bind(NewsFeedRepositoryInterface::class, NewsFeedRepository::class);
        $this->app->bind(NewsFeedServiceInterface::class, NewsFeedService::class);
        
    }

    public function boot()
    {
        //
    }
}
