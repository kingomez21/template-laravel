<?php

namespace Core\Modules\Users;

use Core\Modules\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/list', [UserController::class, 'index']);
