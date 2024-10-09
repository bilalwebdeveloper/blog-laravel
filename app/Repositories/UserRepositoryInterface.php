<?php
// app/Repositories/UserRepositoryInterface.php

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function createUser(array $data);

    public function findByCredentials(array $credentials);

    public function updateLastLogin(int $userId);

    public function getUserById(int $userId);

    public function updateUsername(int $userId, string $username);
}
