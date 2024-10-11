<?php

namespace App\Repositories;

interface ArticleRepositoryInterface
{
    public function findArticleById($id);
    public function createArticle(array $data);
    public function updateArticle($id, array $data);
    public function deleteArticle($id);
    public function searchArticles(string $query, ?string $date = null, ?string $source = null, ?string $category = null);
    public function getArticles($offset, $limit);
    public function findArticleByUrl($url);
    public function fetchAllSource();
    public function fetchAllAuthors();
    
}
