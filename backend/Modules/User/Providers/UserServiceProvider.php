<?php

namespace Modules\User\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \Modules\User\Interfaces\UserServiceInterface::class,
            \Modules\User\Services\UserService::class
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');
        
        Route::prefix('api/user')
            ->middleware('api')
            ->group(__DIR__ . '/../Routes/api.php');
    }
}
