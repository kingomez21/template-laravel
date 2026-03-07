<?php

namespace Inventory;

use Illuminate\Support\ServiceProvider;

class InventoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registra aquí los ServiceProviders de tus módulos
        $this->app->register(\Inventory\Modules\Product\ProductServiceProvider::class);
        $this->app->register(\Inventory\Modules\Sales\SalesServiceProvider::class);
    }
}
