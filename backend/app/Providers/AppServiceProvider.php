<?php

namespace App\Providers;

use Core\Models\PersonalAccessToken;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Reemplaza el modelo de tokens de Sanctum por nuestra versión tenant-aware,
        // que fuerza la conexión pgsql y respeta el search_path del tenant activo.
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
