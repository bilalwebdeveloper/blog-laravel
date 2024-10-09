<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NewsFeedService; // Import the service
use App\Http\Requests\StoreUserPreferencesRequest;

class UserPreferenceController extends Controller
{
    protected $newsFeedService;

    public function __construct(NewsFeedService $newsFeedService)
    {
        $this->newsFeedService = $newsFeedService;
    }

    public function index(Request $request)
    {
        // Call the service to retrieve the personalized news feed
        return $this->newsFeedService->getPersonalizedFeed();
    }

    public function store(StoreUserPreferencesRequest $request)
    {
        $data = $request->validated(); 

        // Call the service to customize the news feed based on the stored preferences
        return $this->newsFeedService->customizeFeed($data);
    }
}
