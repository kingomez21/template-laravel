<?php

namespace Core\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

/**
 * Sobreescribe el modelo de tokens de Sanctum para forzar la conexión pgsql.
 * Esto permite que Sanctum busque los tokens en el schema del tenant activo,
 * ya que TenantMiddleware setea el search_path antes de que auth:sanctum ejecute.
 */
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    protected $connection = 'pgsql';
}
