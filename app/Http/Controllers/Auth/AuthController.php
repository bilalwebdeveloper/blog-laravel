<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginValidationRequest;
use App\Http\Requests\RegisterUserValidationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{

    // User registration method
    public function register(RegisterUserValidationRequest $request)
    {

        $request["ip"] = $request->ip();
        $request["device"] = $request->userAgent();
        $request["name"] = $request->firstName.' '.$request->lastName;

        $user = User::create($request->all());
        $token = $user->createToken('authToken')->plainTextToken;

        $data = [
          'user' => $user,
          'token' => $token
        ];
        return $this->sendResponse($data, 'User registered successfully.');
    }

    // User login method
    public function login(LoginValidationRequest $request)
    {

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;
            $user->update(['last_login' => now()]);
            $data = [
                'user' => $user,
                'token' => $token
            ];

            return $this->sendResponse($data, 'Login successful');
        } else {
            return $this->sendError('Login Failed',['error' => 'Invalid credentials'], 401);
        }
    }



    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->tokens()->delete();

            return $this->sendResponse([], 'User logged out successfully');
        }

        return $this->sendError('Failed',['message' => 'Unauthenticated'], 401);

    }

}
