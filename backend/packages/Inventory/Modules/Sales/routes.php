<?php

namespace Inventory\Modules\Sales;

use Inventory\Modules\Sales\SalesController;
use Illuminate\Support\Facades\Route;

Route::get('/list', [SalesController::class, 'index']);
