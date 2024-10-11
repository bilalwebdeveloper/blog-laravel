<?php
// app/Services/ArticleServiceInterface.php

namespace App\Services;

use Illuminate\Http\Request;

interface CategoryArticleServiceInterface
{
    public function getCategoryArticles(Request $request, $categoryId = null);
    public function getSingleHomeArticle();
    public function getSubCategoriesArticles($parent_id);
    public function getCategoriesArticles();
}
