<?php

namespace Inventory\Modules\Product;

use Inventory\Modules\Product\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/list', [ProductController::class, 'index']);
