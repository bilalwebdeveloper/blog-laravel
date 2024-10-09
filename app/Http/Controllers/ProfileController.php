<?php

// app/Http/Controllers/ProfileController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;

class ProfileController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display the authenticated user's profile information.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        try {
            // Use the service to get the user profile
            $profile = $this->userService->getUserProfile();
            return response()->json($profile, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * Update the authenticated user's name.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
        ]);

        try {
            // Use the service to update the user profile
            $message = $this->userService->updateUsername($request->only('username'));
            return response()->json(['message' => $message], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
