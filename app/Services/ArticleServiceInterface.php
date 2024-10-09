<?php
// app/Services/ArticleServiceInterface.php

namespace App\Services;

use Illuminate\Http\Request;

interface ArticleServiceInterface
{
    public function findArticleById($id);
    public function createArticle(array $data);
    public function updateArticle($id, array $data);
    public function deleteArticle($id);
    public function searchArticles(string $query, ?string $date = null, ?string $source = null, ?string $category = null);
    public function fetchArticlesFromApi(Request $request);
    public function getSubcategoryArticles(Request $request, $categoryId = null);
    public function getSingleHomeArticle();
    public function fetchAllSource();
    public function fetchAllAuthors();
}
