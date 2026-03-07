<?php

namespace Core\Modules\Users;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \Core\Modules\Users\IUser::class,
            \Core\Modules\Users\UserService::class
        );

        $this->commands([
            \Core\Modules\Users\Commands\UserSync::class,
        ]);
    }

    public function boot(): void
    {
        Route::prefix('api/users')
            ->middleware('api')
            ->group(__DIR__ . '/routes.php');
    }
}
