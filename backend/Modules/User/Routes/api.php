<?php

use Illuminate\Support\Facades\Route;

// Rutas para el módulo User
// Ya tienen el prefijo /api/user

Route::get('/', function () {
    return response()->json(['message' => 'API de User funcionando']);
});
