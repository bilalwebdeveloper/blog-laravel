<?php

namespace App\Services;

use GuzzleHttp\Client;

class NewsService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getNewsFromNewsAPI($keyword)
    {
        $apiKey = env('NEWS_API_KEY');
        $url = "https://newsapi.org/v2/everything?q={$keyword}&apiKey={$apiKey}";

        $response = $this->client->get($url);
        return json_decode($response->getBody(), true);
    }

    public function getNewsFromOpenNews($keyword)
    {
        $apiKey = env('OPENNEWS_API_KEY');
        $url = "https://opennewsapi.com/v1/news?query={$keyword}&apiKey={$apiKey}";

        $response = $this->client->get($url);
        return json_decode($response->getBody(), true);
    }

    public function getNewsFromNewsCred($keyword)
    {
        $apiKey = env('NEWSCRED_API_KEY');
        $url = "https://newscredapi.com/v1/articles?query={$keyword}&apiKey={$apiKey}";

        $response = $this->client->get($url);
        return json_decode($response->getBody(), true);
    }
}
