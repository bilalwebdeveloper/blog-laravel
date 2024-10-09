<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserServiceInterface;
use App\Http\Requests\LoginValidationRequest;
use App\Http\Requests\RegisterUserValidationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    // User registration method
    public function register(RegisterUserValidationRequest $request)
    {
        try {
            $data = $request->validated();
            $data['ip'] = $request->ip();
            $data['device'] = $request->userAgent();
            $data['name'] = $request->firstName . ' ' . $request->lastName;

            $user = $this->userService->register($data);
            $token = $user->createToken('authToken')->plainTextToken;

            $responseData = [
                'user' => $user,
                'token' => $token
            ];

            return $this->sendResponse($responseData, 'User registered successfully.');
        } catch (Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return $this->sendError('Registration failed', ['error' => 'An error occurred while registering the user'], 500);
        }
    }

    // User login method
    public function login(LoginValidationRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $user = $this->userService->login($credentials);

            if ($user) {
                $token = $user->createToken('authToken')->plainTextToken;
                return $this->sendResponse(['user' => $user, 'token' => $token], 'Login successful');
            }

            return $this->sendError('Login failed', ['error' => 'Invalid credentials'], 401);
        } catch (Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return $this->sendError('Login failed', ['error' => 'An error occurred while login the user'], 500);
        }
    }

    // User logout method
    public function logout(Request $request)
    {
        try {
            $user = $this->userService->logout();

            if ($user) {
                // Revoke tokens if the user exists.
                $user->tokens()->delete();
                return $this->sendResponse([], 'User logged out successfully');
            }

            return $this->sendError('Logout failed', ['message' => 'Unauthenticated'], 401);
        } catch (Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return $this->sendError('Logout failed', ['error' => 'An error occurred while logging out'], 500);
        }
    }
}
