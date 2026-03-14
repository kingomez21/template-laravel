<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MetadataController;
use App\Http\Controllers\NotificationTemplateController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('api');
Route::post('/login',    [AuthController::class, 'login'])
    ->middleware('api');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user',   fn (Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('companies', CompanyController::class);
    Route::apiResource('tenants', TenantController::class);
    Route::apiResource('messages', MessageController::class);
    Route::apiResource('metadata', MetadataController::class);
    Route::apiResource('notification-templates', NotificationTemplateController::class);
    Route::apiResource('tenant-services', TenantServiceController::class);
});
