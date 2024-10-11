<?php
// app/Services/ArticleService.php

namespace App\Services;

use App\Repositories\ArticleRepositoryInterface;
use App\Models\Category;
use App\Models\Article;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ArticleService implements ArticleServiceInterface
{
    protected $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function findArticleById($id)
    {
        return $this->articleRepository->findArticleById($id);
    }

    public function createArticle(array $data)
    {
        return $this->articleRepository->createArticle($data);
    }

    public function updateArticle($id, array $data)
    {
        return $this->articleRepository->updateArticle($id, $data);
    }

    public function deleteArticle($id)
    {
        return $this->articleRepository->deleteArticle($id);
    }

    public function searchArticles(string $query, ?string $date = null, ?string $source = null, ?string $category = null)
    {
        return $this->articleRepository->searchArticles($query, $date, $source, $category);
    }

    function fetchAllSource() {
        return $this->articleRepository->fetchAllSource();
    }
    function fetchAllAuthors() {
        return $this->articleRepository->fetchAllAuthors();
    }
}
