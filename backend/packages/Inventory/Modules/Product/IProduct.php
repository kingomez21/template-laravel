<?php

namespace Inventory\Modules\Product;

interface IProduct
{
    public function getProducts(): array;
    public function createProduct(array $data): array;
}
