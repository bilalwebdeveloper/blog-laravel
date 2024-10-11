<?php
// app/Services/UserService.php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Exception;

class UserService implements UserServiceInterface
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data)
    {
        // Hash the password before creating the user
        $data['password'] = Hash::make($data['password']);
        
        return $this->userRepository->createUser($data);
    }

    public function login(array $credentials)
    {
        $user = $this->userRepository->findByCredentials($credentials);

        if ($user) {
            // $this->userRepository->updateLastLogin($user->id);
            return $user;
        }

        return null;
    }

    public function logout()
    {
        $user = Auth::user();
        if ($user) {
            return $user;
        }
        return null;
    }

    public function getUserProfile()
    {
        $user = Auth::user();

        if (!$user) {
            throw new Exception('User not authenticated.');
        }

        return [
            'name' => $user->name,
            'email' => $user->email,
        ];
    }

    public function updateUsername(array $data)
    {
        $user = Auth::user();

        if (!$user) {
            throw new Exception('User not authenticated.');
        }

        $this->userRepository->updateUsername($user->id, $data['username']);

        return 'Profile updated successfully.';
    }
}
