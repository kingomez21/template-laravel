<?php

namespace Inventory\Modules\Sales;

class SalesService implements ISales
{
    public function __construct()
    {
        // Inyecta dependencias si es necesario
    }

    public function getSales(): array
    {
        return [
            ["id" => 1, "product_id" => 1, "quantity" => 2, "total" => 200],
            
        ];
    }
}
