<?php

namespace Modules\Inventario\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class InventarioServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \Modules\Inventario\Interfaces\InventarioServiceInterface::class,
            \Modules\Inventario\Services\InventarioService::class
        );

        $this->commands([
            \Modules\Inventario\Console\Commands\ProductoSync::class,
        ]);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');

        Route::prefix('api/inventario')
            ->middleware('api')
            ->group(__DIR__ . '/../Routes/api.php');
    }
}
