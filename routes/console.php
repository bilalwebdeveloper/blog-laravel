<?php
use App\Console\Commands\FetchArticleData;
use Illuminate\Support\Facades\Schedule;

Schedule::command('fetch:article-data')->daily();

