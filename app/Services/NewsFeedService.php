<?php
// app/Services/NewsFeedService.php

namespace App\Services;

use App\Repositories\NewsFeedRepositoryInterface;
use Exception;

class NewsFeedService implements NewsFeedServiceInterface
{
    protected $newsFeedRepository;

    public function __construct(NewsFeedRepositoryInterface $newsFeedRepository)
    {
        $this->newsFeedRepository = $newsFeedRepository;
    }

    public function customizeFeed(array $preferences)
    {
        try {
            // Check if the user is authenticated
            $user = auth()->user();
            if (!$user) {
                throw new Exception('User not authenticated.');
            }

            // Update or create user preferences using the repository
            $userPreferences = $this->newsFeedRepository->updateOrCreateUserPreferences($user->id, $preferences);

            return response()->json($userPreferences, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getPersonalizedFeed()
    {
        try {
            // Check if the user is authenticated
            $user = auth()->user();
            if (!$user) {
                throw new Exception('User not authenticated.');
            }

            // Retrieve user preferences using the repository
            $preferences = $this->newsFeedRepository->getUserPreferences($user->id);
            if (!$preferences) {
                throw new Exception('User preferences not found.');
            }

            // Decode JSON fields
            $preferences->preferred_sources = json_decode($preferences->preferred_sources, true);
            $preferences->preferred_categories = json_decode($preferences->preferred_categories, true);
            $preferences->preferred_authors = json_decode($preferences->preferred_authors, true);

            // Return the decoded preferences as JSON
            return response()->json($preferences, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
