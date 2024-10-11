<?php

namespace App\Repositories;

interface CategoryArticleRepositoryInterface
{
    public function getCategoryArticles($categoryId, $offset, $limit);
    public function countArticlesByCategory($categoryId);
    public function getFirstArticleByCategoryId($categoryId);
    public function getArticlesByCategoryId($categoryId);
    public function getLatestArticlesBySubCategories($subCategoryIds);
    
    
}
