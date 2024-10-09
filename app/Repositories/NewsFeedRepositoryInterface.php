<?php
// app/Repositories/NewsFeedRepositoryInterface.php

namespace App\Repositories;

interface NewsFeedRepositoryInterface
{
    public function getUserPreferences(int $userId);

    public function updateOrCreateUserPreferences(int $userId, array $data);
}
