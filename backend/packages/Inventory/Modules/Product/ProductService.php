<?php

namespace Inventory\Modules\Product;

use Inventory\Models\Product;

class ProductService implements IProduct
{
    public function __construct()
    {
        // Inyecta dependencias si es necesario
    }

    public function getProducts(): array
    {
       $products = Product::all();
       return $products->toArray();
    }

    public function createProduct(array $data): array
    {
        $product = Product::create($data);
        return $product->toArray();
    }
}
