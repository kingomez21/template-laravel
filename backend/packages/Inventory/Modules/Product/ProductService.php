<?php

namespace Inventory\Modules\Product;

class ProductService implements IProduct
{
    public function __construct()
    {
        // Inyecta dependencias si es necesario
    }

    public function getProducts(): array
    {
        // Implementa la lógica para obtener los productos
        return [
            ['id' => 1, 'name' => 'Producto 1', 'price' => 100],
            ['id' => 2, 'name' => 'Producto 2', 'price' => 200],
        ];
    }
}
