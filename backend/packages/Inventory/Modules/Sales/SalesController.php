<?php

namespace Inventory\Modules\Sales;

use App\Http\Controllers\Controller;
use Inventory\Modules\Product\IProduct;

class SalesController extends Controller
{
    public function __construct(
        protected ISales $service, 
        protected IProduct $productService
    )
    {
    }

    public function index()
    {
        // solicto la lista de usuarios en ventas
        $response = [
            "sales" => $this->service->getSales(),
            "products" => $this->productService->getProducts(),
        ];

        return response()->json($response);
    }
}
