<?php

namespace Inventory\Modules\Sales;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class SalesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \Inventory\Modules\Sales\ISales::class,
            \Inventory\Modules\Sales\SalesService::class
        );
    }

    public function boot(): void
    {
        Route::prefix('api/sales')
            ->middleware('api')
            ->middleware('tenant')
            ->group(__DIR__ . '/routes.php');
    }
}
