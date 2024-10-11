<?php

namespace App\Repositories;

interface CategoryArticleRepositoryInterface
{
    public function getCategoryArticles($categoryId, $offset, $limit);
    public function getArticlesByCategoryId($categoryId);
    public function countArticlesByCategory($categoryId);
    public function getFirstArticleByCategoryId($categoryId);
    public function getLatestArticlesBySubCategories($subCategoryIds);
    
    
}
