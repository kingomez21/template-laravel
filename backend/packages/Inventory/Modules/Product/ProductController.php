<?php

namespace Inventory\Modules\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected IProduct $service)
    {
    }

    public function index()
    {
        return response()->json($this->service->getProducts());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
        ]);

        return response()->json($this->service->createProduct($data));
    }
}
