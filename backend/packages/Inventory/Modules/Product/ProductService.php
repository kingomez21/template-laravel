<?php

namespace Inventory\Modules\Product;

use App\Util\WithinSchema;
use Inventory\Models\Product;

class ProductService implements IProduct
{
    
    public function getProducts(): array
    {
        return WithinSchema::queryTenant(function () {
            return Product::all()->toArray();
        });
    }

    public function createProduct(array $data): array
    {
        return WithinSchema::queryTenant(function () use ($data) {
            return Product::create($data)->toArray();
        });
    }
}
