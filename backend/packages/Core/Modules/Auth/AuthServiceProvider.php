<?php

namespace Core\Modules\Auth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \Core\Modules\Auth\IAuth::class,
            \Core\Modules\Auth\AuthService::class
        );
    }

    public function boot(): void
    {
        Route::prefix('api/auth-tenant')
            ->middleware('api')
            ->group(__DIR__ . '/routes.php');
    }
}
