<?php

namespace App\Util;

use Illuminate\Support\Facades\DB;

class WithinSchema
{
    /**
     * Ejecuta un callable dentro de una transacción con el search_path
     * apuntando al schema del tenant activo.
     *
     * Ventajas:
     *  - El search_path solo está activo durante la transacción; al hacer
     *    COMMIT/ROLLBACK PostgreSQL lo descarta automáticamente.
     *  - Compatible con Octane/RoadRunner: el worker no retiene estado sucio
     *    entre requests porque el cambio nunca sale de la transacción.
     *  - Si algo falla dentro del callback, el ROLLBACK deshace tanto los
     *    datos como cualquier DDL temporal, dejando la conexión limpia.
     *
     * @template T
     * @param  callable(): T  $callback
     * @return T
     */
    public static function queryTenant(callable $callback): mixed
    {
        $schema = app('tenant.schema');

        return DB::connection('pgsql')->transaction(function () use ($schema, $callback) {
            // SET LOCAL aplica solo mientras dure la transacción actual.
            // Al hacer COMMIT o ROLLBACK, PostgreSQL restaura el search_path anterior.
            DB::connection('pgsql')->statement("SET LOCAL search_path TO \"{$schema}\", public");

            return $callback();
        });
    }
}