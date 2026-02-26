<?php

namespace Modules\Test\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class TestServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \Modules\Test\Interfaces\TestServiceInterface::class,
            \Modules\Test\Services\TestService::class
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        
        Route::prefix('api/test')
            ->middleware('api')
            ->group(__DIR__ . '/../Routes/api.php');
    }
}
