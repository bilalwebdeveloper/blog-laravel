<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        // Define categories and subcategories
        $categories = [
            'Technology' => ['AI', 'Blockchain', 'Cybersecurity'],
            'Health' => ['Mental Health', 'Nutrition', 'Fitness'],
            'Business' => ['Startups', 'Marketing', 'Finance'],
            'Entertainment' => ['Movies', 'Music', 'Gaming'],
            'Science' => ['Astronomy', 'Physics', 'Biology']
        ];

        foreach ($categories as $categoryName => $subcategories) {
            // Create parent category
            $category = Category::create([
                'name' => $categoryName,
                'parent_id' => null,
            ]);

            // Create subcategories
            foreach ($subcategories as $subcategoryName) {
                Category::create([
                    'name' => $subcategoryName,
                    'parent_id' => $category->id,
                ]);
            }
        }
    }
}
