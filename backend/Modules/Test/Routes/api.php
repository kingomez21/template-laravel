<?php

use Illuminate\Support\Facades\Route;

// Rutas para el módulo Test
// Ya tienen el prefijo /api/test

Route::get('/', function () {
    return response()->json(['message' => 'API de Test funcionando']);
});
