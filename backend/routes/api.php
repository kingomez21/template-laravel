<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('api');
Route::post('/login',    [AuthController::class, 'login'])
    ->middleware('api');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user',   fn (Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout']);
});
