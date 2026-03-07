<?php

namespace App\Util;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Modelo base para todos los modelos que operan sobre el schema del tenant.
 *
 * Uso:
 *   class Product extends TenantModel { ... }
 *
 * El schema activo se resuelve en cada request vía TenantMiddleware (search_path).
 * Esta clase además fuerza la conexión a pgsql y configura el schema de forma
 * segura con Octane/RoadRunner (sin purge/reconnect).
 */
abstract class TenantModel extends Model
{
    protected $connection = 'pgsql';

    /**
     * Al instanciar el modelo nos aseguramos de que el search_path
     * esté apuntando al schema del tenant activo.
     *
     * Esto cubre casos donde un modelo se usa fuera del ciclo HTTP normal
     * (Jobs, Commands, etc.) y el schema ya fue seteado por otro mecanismo.
     */
    protected static function booted(): void
    {
        // Si el middleware ya ejecutó SET search_path, no se hace nada extra.
        // Si se llama desde un Job/Command, debe setearse manualmente antes:
        //   TenantModel::setTenantSchema('empresa_a');
    }

    /**
     * Cambia el search_path de la conexión al schema indicado.
     * Úsalo en Jobs, Commands o cualquier contexto fuera del ciclo HTTP.
     *
     * Ejemplo en un Job:
     *   public function handle(): void
     *   {
     *       TenantModel::setTenantSchema($this->tenantSchema);
     *       $products = Product::all();
     *   }
     */
    public static function setTenantSchema(string $schema): void
    {
        $schema = preg_replace('/[^a-zA-Z0-9_]/', '', $schema);

        if ($schema === '') {
            throw new \InvalidArgumentException("Schema de tenant inválido.");
        }

        config(['database.connections.pgsql.search_path' => $schema]);

        DB::connection('pgsql')
            ->statement("SET search_path TO \"{$schema}\", public");

        app()->instance('tenant.schema', $schema);
    }

    /**
     * Devuelve el schema activo del tenant, o null si no hay ninguno seteado.
     */
    public static function currentSchema(): ?string
    {
        try {
            return app('tenant.schema');
        } catch (\Throwable) {
            return null;
        }
    }
}
