<?php

use Core\Modules\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login',    [AuthController::class, 'login']);
Route::post('/r', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('/list', [AuthController::class, 'register']);
