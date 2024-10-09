<?php
// app/Services/MenuService.php

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Models\UserPreference;

class MenuService implements MenuServiceInterface
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getHeaderMenu($userId = null)
    {
        if ($userId) {
            return $this->getUserHeaderMenu($userId);
        }
        return $this->getCategoryMenu(5);
    }

    public function getFooterMenu($userId = null)
    {
        if ($userId) {
            return $this->getUserFooterMenu($userId);
        }
        return $this->getCategoryMenu(5);
    }

    public function getCategoryMenu($limit)
    {
        $parentCategories = $this->categoryRepository->getParentCategories($limit);

        return $parentCategories->map(function ($parentCategory) {
            return [
                'parent_category_id' => $parentCategory->id,
                'parent_category' => $parentCategory->name,
                'subcategories' => $parentCategory->subcategories->map(function ($subcategory) {
                    return [
                        'id' => $subcategory->id,
                        'name' => $subcategory->name,
                    ];
                }),
            ];
        });
    }

    public function getUserCategoryMenu($userId, $limit)
    {
        $userPreferences = UserPreference::where('user_id', $userId)->first();

        if ($userPreferences) {
            $preferredCategories = $userPreferences->preferred_categories;

            return $this->categoryRepository->getCategoriesByIds($preferredCategories, $limit)
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                    ];
                });
        }

        return $this->getCategoryMenu($limit);
    }

    private function getUserHeaderMenu($userId)
    {
        return $this->getUserCategoryMenu($userId, 5);
    }

    private function getUserFooterMenu($userId)
    {
        return $this->getUserCategoryMenu($userId, 5);
    }
}
