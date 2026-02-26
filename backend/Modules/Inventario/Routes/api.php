<?php

use Illuminate\Support\Facades\Route;

// Rutas para el módulo Inventario
// Ya tienen el prefijo /api/inventario

Route::get('/', function () {
    return response()->json(['message' => 'API de Inventario funcionando']);
});
