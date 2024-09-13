<?php

// routes/console.php

use App\Console\Commands\FetchApiData;
use Illuminate\Support\Facades\Schedule;

Schedule::command(FetchArticleData::class)->hourly();

