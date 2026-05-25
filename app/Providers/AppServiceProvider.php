<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use App\Services\UserService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(UserService::class);
    }

    public function boot(): void
    {
        // Use Tailwind-compatible pagination views
        Paginator::useBootstrapFive();
    }
}
