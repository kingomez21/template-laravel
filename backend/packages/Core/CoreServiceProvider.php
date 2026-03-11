<?php

namespace Core;

use Core\Modules\Users\UserServiceProvider;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {

        // register other service providers here
        $this->app->register(UserServiceProvider::class);

        $this->app->register(\Core\Modules\Auth\AuthServiceProvider::class);
    }
}

