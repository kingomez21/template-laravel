<?php

namespace Inventory\Modules\Product;

use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function __construct(protected IProduct $service)
    {
    }

    public function index()
    {
        return response()->json($this->service->getProducts());
    }
}
