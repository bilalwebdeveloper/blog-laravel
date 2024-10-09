<?php
// app/Repositories/UserRepository.php

namespace App\Repositories;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function createUser(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'ip' => $data['ip'],
            'device' => $data['device'],
        ]);
    }

    public function findByCredentials(array $credentials)
    {
        return auth()->attempt($credentials) ? auth()->user() : null;
    }

    public function updateLastLogin(int $userId)
    {
        return User::where('id', $userId)->update(['last_login' => now()]);
    }

    public function getUserById(int $userId)
    {
        return User::find($userId);
    }

    public function updateUsername(int $userId, string $username)
    {
        return User::where('id', $userId)->update(['name' => $username]);
    }
}
