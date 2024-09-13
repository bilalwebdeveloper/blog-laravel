<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Validator;

class UserPreferenceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user_id;

        if (!$user) {
            return response()->json(['error' => 'Please sign up or log in first.'], 401);
        }

        $preferences = UserPreference::where('user_id', $user_id)->first();
        return response()->json($preferences, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'preferred_sources' => 'nullable|string',
            'preferred_categories' => 'nullable|string',
            'preferred_authors' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Please sign up or log in first.'], 400);
        }

        $preferences = UserPreference::updateOrCreate(
            ['user_id' => $request->user_id],
            $request->only('preferred_sources', 'preferred_categories', 'preferred_authors')
        );

        return response()->json($preferences, 200);
    }
}

