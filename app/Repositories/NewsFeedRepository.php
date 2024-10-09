<?php
// app/Repositories/NewsFeedRepository.php

namespace App\Repositories;

use App\Models\UserPreference;

class NewsFeedRepository implements NewsFeedRepositoryInterface
{
    public function getUserPreferences(int $userId)
    {
        // Retrieve user preferences based on the user ID
        return UserPreference::where('user_id', $userId)->first();
    }

    public function updateOrCreateUserPreferences(int $userId, array $data)
    {
        // Store or update user preferences based on user ID
        return UserPreference::updateOrCreate(
            ['user_id' => $userId],
            [
                'preferred_sources' => json_encode($data['sources']),
                'preferred_categories' => json_encode($data['categories']),
                'preferred_authors' => json_encode($data['authors']),
            ]
        );
    }
}
