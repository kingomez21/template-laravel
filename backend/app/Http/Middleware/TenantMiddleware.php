<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Cabecera HTTP usada como fuente alternativa al subdominio.
     * El cliente puede enviar:  X-Tenant: empresa_a
     */
    private const HEADER = 'X-Tenant';

    /**
     * Schema utilizado automáticamente en entorno local (localhost).
     * Configurable vía DEV_TENANT_SCHEMA en el .env.
     */
    private const DEV_SCHEMA = 'laravel_app';

    /**
     * Dominio raíz de la aplicación (sin subdominio).
     * Se usa para extraer el subdominio: {tenant}.miapp.com
     * Configura APP_DOMAIN en tu .env o ajusta este valor.
     */
    private string $rootDomain;

    public function __construct()
    {
        $this->rootDomain = config('app.domain', env('APP_DOMAIN', 'localhost'));
    }

    public function handle(Request $request, Closure $next): Response
    {
        $schema = $this->resolveTenantSchema($request);

        if ($schema === null) {
            return response()->json([
                'message' => 'Tenant no identificado. Envía la cabecera ' . self::HEADER . ' o accede mediante un subdominio válido.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $schema = $this->sanitizeSchema($schema);

        if ($schema === '') {
            return response()->json([
                'message' => 'Identificador de tenant inválido.',
            ], Response::HTTP_BAD_REQUEST);
        }

        // ─── Octane/RoadRunner safe ───────────────────────────────────────────
        // Se usa DB::connection('pgsql') explícitamente porque la conexión por
        // defecto puede ser sqlite. SET search_path aplica a la sesión activa.
        // También se actualiza el config para que reconexiones hereden el schema.
        // El método terminate() lo resetea al finalizar el request.
        config(['database.connections.pgsql.search_path' => $schema]);
        DB::connection('pgsql')->statement("SET search_path TO \"{$schema}\", public");
        // ─────────────────────────────────────────────────────────────────────

        // Exponer el schema en el request y en el contenedor de la app
        $request->merge(['_tenant_schema' => $schema]);
        app()->instance('tenant.schema', $schema);

        return $next($request);
    }

    /**
     * Se ejecuta DESPUÉS de enviar la respuesta (Octane lo invoca correctamente).
     * Resetea el search_path para que el worker quede limpio para el próximo request.
     */
    public function terminate(Request $request, Response $response): void
    {
        try {
            config(['database.connections.pgsql.search_path' => 'public']);
            DB::connection('pgsql')->statement('SET search_path TO public');
        } catch (\Throwable) {
            // Si la conexión ya fue cerrada o no se llegó a abrir, no hay nada que resetear
        }
    }

    private function resolveTenantSchema(Request $request): ?string
    {
        // 1. Cabecera X-Tenant
        if ($request->hasHeader(self::HEADER)) {
            $value = trim($request->header(self::HEADER));
            if ($value !== '') {
                return $value;
            }
        }

        // 2. Subdominio
        $subdomain = $this->extractSubdomain($request->getHost());
        if ($subdomain !== null) {
            return $subdomain;
        }

        // 3. Fallback local: si es localhost o APP_ENV=local, usar schema de desarrollo
        if ($this->isLocalEnvironment($request)) {
            return env('DEV_TENANT_SCHEMA', self::DEV_SCHEMA);
        }

        return null;
    }

    /**
     * Determina si la petición proviene de un entorno local de desarrollo.
     */
    private function isLocalEnvironment(Request $request): bool
    {
        if (app()->environment('local')) {
            return true;
        }

        $host = strtolower(explode(':', $request->getHost())[0]);

        return in_array($host, ['localhost', '127.0.0.1', '::1'], true);
    }

    /**
     * Devuelve el primer segmento del host si es un subdominio del rootDomain.
     * Ejemplos:
     *   empresa_a.miapp.com  → 'empresa_a'
     *   miapp.com            → null  (sin subdominio)
     *   localhost             → null
     */
    private function extractSubdomain(string $host): ?string
    {
        // Normalizar: quitar el puerto si viene incluido
        $host = strtolower(explode(':', $host)[0]);
        $root = strtolower($this->rootDomain);

        if ($host === $root) {
            return null;
        }

        $suffix = '.' . $root;
        if (str_ends_with($host, $suffix)) {
            $sub = substr($host, 0, -strlen($suffix));
            // Solo tomamos el subdominio más cercano (izquierda)
            $parts = explode('.', $sub);
            return end($parts) ?: null;
        }

        return null;
    }

    /**
     * Permite solo letras, dígitos y guiones bajos para evitar SQL injection
     * en el nombre del schema (no se pueden usar bindings en SET search_path).
     */
    private function sanitizeSchema(string $schema): string
    {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $schema);
    }
}

