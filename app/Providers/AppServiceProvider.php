<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\QuestionAnswering\ChatQAService;
use App\Services\QuestionAnswering\QAServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(QAServiceInterface::class, ChatQAService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
