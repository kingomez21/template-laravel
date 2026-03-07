<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MigrateTenantsCommand extends Command
{
    protected $signature = 'tenants:migrate
                            {--fresh    : Descarta todas las tablas y re-ejecuta todas las migraciones}
                            {--tenant=  : Migra únicamente el schema indicado (ej: --tenant=empresa1)}
                            {--pretend  : Muestra el SQL sin ejecutarlo}';

    protected $description = 'Ejecuta las migraciones de todos los paquetes en cada schema de tenant (PostgreSQL)';

    /**
     * Array de tenants para pruebas.
     * TODO: reemplazar con consulta a la tabla `tenants` cuando exista:
     *   $this->tenants = Tenant::all()->map(fn($t) => ['name' => $t->name, 'schema' => $t->schema])->toArray();
     */
    private array $tenants = [
        ['name' => 'Testing', 'schema' => 'laravel_app'],
        //['name' => 'Empresa B', 'schema' => 'empresa_b'],
    ];

    public function handle(): int
    {
        $tenants = $this->resolveTenants();

        if (empty($tenants)) {
            return Command::FAILURE;
        }

        $migrationPaths = $this->discoverMigrationPaths();

        if (empty($migrationPaths)) {
            $this->warn('⚠️  No se encontraron carpetas Migrations en ningún paquete.');
            return Command::SUCCESS;
        }

        $this->line('');
        $this->line('📦 Rutas de migración encontradas:');
        foreach ($migrationPaths as $path) {
            $this->line('   • ' . str_replace(base_path() . '/', '', $path));
        }
        $this->line('');

        $command = $this->option('fresh') ? 'migrate:fresh' : 'migrate';
        $failed  = [];

        foreach ($tenants as $tenant) {
            $schema = $tenant['schema'];
            $name   = $tenant['name'];

            $this->line("┌─ 🏢 Tenant: <comment>{$name}</comment> (schema: <comment>{$schema}</comment>)");

            if (!$this->createSchemaIfNotExists($schema)) {
                $failed[] = $schema;
                $this->line("└─ ❌ Abortado por error al crear el schema.\n");
                continue;
            }

            // Apuntar la conexión pgsql al schema del tenant
            config(['database.connections.pgsql.search_path' => $schema]);
            DB::purge('pgsql');

            foreach ($migrationPaths as $path) {
                $relativePath = str_replace(base_path() . '/', '', $path);
                $this->line("│  📂 {$relativePath}");

                $options = [
                    '--database' => 'pgsql',
                    '--path'     => $relativePath,
                    '--force'    => true,
                ];

                if ($this->option('pretend')) {
                    $options['--pretend'] = true;
                }

                Artisan::call($command, $options, $this->output);
            }

            $this->line("└─ ✅ Completado.\n");
        }

        // Restaurar search_path por defecto al terminar
        config(['database.connections.pgsql.search_path' => 'public']);
        DB::purge('pgsql');

        if (!empty($failed)) {
            $this->error('Los siguientes tenants fallaron: ' . implode(', ', $failed));
            return Command::FAILURE;
        }

        $this->info('✅ Todas las migraciones de tenants completadas.');
        return Command::SUCCESS;
    }

    /**
     * Resuelve la lista de tenants según la opción --tenant.
     */
    private function resolveTenants(): array
    {
        $specific = $this->option('tenant');

        if ($specific) {
            $filtered = array_values(
                array_filter($this->tenants, fn($t) => $t['schema'] === $specific)
            );

            if (empty($filtered)) {
                $this->error("❌ No se encontró ningún tenant con schema '{$specific}'.");
                $this->line('   Schemas disponibles: ' . implode(', ', array_column($this->tenants, 'schema')));
                return [];
            }

            return $filtered;
        }

        return $this->tenants;
    }

    /**
     * Descubre todas las carpetas Migrations dentro de packages/*.
     * Solo incluye carpetas que contengan al menos un archivo .php.
     */
    private function discoverMigrationPaths(): array
    {
        $paths = [];

        foreach (File::directories(base_path('packages')) as $packageDir) {
            $migrationsDir = "{$packageDir}/Migrations";

            if (File::isDirectory($migrationsDir) && !empty(File::files($migrationsDir))) {
                $paths[] = $migrationsDir;
            }
        }

        return $paths;
    }

    /**
     * Crea el schema de PostgreSQL si no existe.
     */
    private function createSchemaIfNotExists(string $schema): bool
    {
        try {
            // Usamos identificador entre comillas dobles para soportar nombres con guiones bajos, etc.
            DB::connection('pgsql')->statement("CREATE SCHEMA IF NOT EXISTS \"{$schema}\"");
            $this->line("│  ✔ Schema <comment>\"{$schema}\"</comment> verificado/creado.");
            return true;
        } catch (\Throwable $e) {
            $this->error("│  Error al crear el schema \"{$schema}\": " . $e->getMessage());
            return false;
        }
    }
}
