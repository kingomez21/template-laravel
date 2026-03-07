<?php

use Core\CoreServiceProvider;
use Inventory\InventoryServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    CoreServiceProvider::class,
    InventoryServiceProvider::class,
];
