<?php

namespace App\Repositories;

interface ArticleRepositoryInterface
{
    public function findArticleById($id);
    public function createArticle(array $data);
    public function updateArticle($id, array $data);
    public function deleteArticle($id);
    public function searchArticles(string $query, ?string $date = null, ?string $source = null, ?string $category = null);
    public function findArticleByUrl($url);
    public function getArticles($offset, $limit);
    public function getSubcategoryArticles($categoryId, $offset, $limit);
    public function countArticlesByCategory($categoryId);
    public function getFirstArticleByCategoryId($categoryId);
    public function fetchAllSource();
    public function fetchAllAuthors();
    
}
