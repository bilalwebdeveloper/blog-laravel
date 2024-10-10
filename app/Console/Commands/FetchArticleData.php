<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\NewsService;

class FetchArticleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:article-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This cron job will get the latest news from multiple APIs and store it in our database';

    /**
     * The NewsService instance.
     *
     * @var \App\Services\NewsService
     */
    protected $newsService;

    /**
     * Create a new command instance.
     *
     * @param \App\Services\NewsService $newsService
     * @return void
     */
    public function __construct(NewsService $newsService)
    {
        parent::__construct();
        $this->newsService = $newsService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Starting to fetch articles from multiple APIs.');

        try {
            // Call the different API methods from the service.
            $this->newsService->fetchArticlesFromNewsData();
            $this->newsService->fetchArticlesFromNewsApi();
            // $this->newsService->fetchArticlesFromNewsCred();
            
            Log::info('Finished fetching articles from all APIs.');
        } catch (\Exception $e) {
            Log::error('Error fetching articles: ' . $e->getMessage());
        }

        return 0;
    }
}
