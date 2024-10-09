<?php
// app/Services/NewsFeedServiceInterface.php

namespace App\Services;

interface NewsFeedServiceInterface
{
    public function customizeFeed(array $preferences);
    public function getPersonalizedFeed();
}
