<?php

namespace Inventory\Modules\Product;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ProductServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \Inventory\Modules\Product\IProduct::class,
            \Inventory\Modules\Product\ProductService::class
        );
    }

    public function boot(): void
    {
        Route::prefix('api/product')
            ->middleware('api')
            ->middleware('tenant')
            ->group(__DIR__ . '/routes.php');
    }
}
