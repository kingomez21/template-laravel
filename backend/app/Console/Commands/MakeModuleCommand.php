<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    /**
     * El nombre y la firma del comando en la terminal.
     */
    protected $signature = 'make:module {name : El nombre del módulo (ej. Facturacion)}';

    /**
     * La descripción que aparece al correr php artisan list.
     */
    protected $description = 'Crea toda la estructura de carpetas y archivos base para un nuevo módulo';

    public function handle()
    {
        // 1. Obtenemos el nombre del módulo y lo formateamos (ej. inventario -> Inventario)
        $name = Str::studly($this->argument('name'));
        $modulePath = base_path("Modules/{$name}");

        // 2. Verificamos si ya existe
        if (File::exists($modulePath)) {
            $this->error("¡El módulo {$name} ya existe!");
            return;
        }

        // 3. Definimos las carpetas a crear
        $directories = [
            'Console',
            'Controllers',
            'Migrations',
            'Exports',
            'Imports',
            'Interfaces',
            'Jobs',
            'Models',
            'Providers',
            'Routes',
            'Services',
        ];

        // 4. Creamos las carpetas
        $this->info("Creando estructura para el módulo: {$name}...");
        foreach ($directories as $dir) {
            File::makeDirectory("{$modulePath}/{$dir}", 0755, true);
        }

        // 5. Generamos los archivos base (Boilerplate)
        $this->generateServiceProvider($name, $modulePath);
        $this->generateRoutes($name, $modulePath);
        $this->generateInterfaceAndService($name, $modulePath);

        $this->info("✅ ¡Módulo {$name} creado con éxito!");
        $this->line("📌 No olvides registrar Modules\\{$name}\\Providers\\{$name}ServiceProvider::class en tu archivo bootstrap/providers.php");
    }

    protected function generateServiceProvider($name, $modulePath)
    {
        $stub = "<?php\n\nnamespace Modules\\{$name}\\Providers;\n\nuse Illuminate\\Support\\ServiceProvider;\nuse Illuminate\\Support\\Facades\\Route;\n\nclass {$name}ServiceProvider extends ServiceProvider\n{\n    public function register(): void\n    {\n        \$this->app->bind(\n            \\Modules\\{$name}\\Interfaces\\{$name}ServiceInterface::class,\n            \\Modules\\{$name}\\Services\\{$name}Service::class\n        );\n    }\n\n    public function boot(): void\n    {\n        \$this->loadMigrationsFrom(__DIR__ . '/../Migrations');\n        \n        Route::prefix('api/" . Str::lower($name) . "')\n            ->middleware('api')\n            ->group(__DIR__ . '/../Routes/api.php');\n    }\n}\n";

        File::put("{$modulePath}/Providers/{$name}ServiceProvider.php", $stub);
    }

    protected function generateRoutes($name, $modulePath)
    {
        $stub = "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\n// Rutas para el módulo {$name}\n// Ya tienen el prefijo /api/" . Str::lower($name) . "\n\nRoute::get('/', function () {\n    return response()->json(['message' => 'API de {$name} funcionando']);\n});\n";

        File::put("{$modulePath}/Routes/api.php", $stub);
    }

    protected function generateInterfaceAndService($name, $modulePath)
    {
        // Generar Interfaz
        $interfaceStub = "<?php\n\nnamespace Modules\\{$name}\\Interfaces;\n\ninterface {$name}ServiceInterface\n{\n    // Define tus métodos aquí\n}\n";
        File::put("{$modulePath}/Interfaces/{$name}ServiceInterface.php", $interfaceStub);

        // Generar Servicio
        $serviceStub = "<?php\n\nnamespace Modules\\{$name}\\Services;\n\nuse Modules\\{$name}\\Interfaces\\{$name}ServiceInterface;\n\nclass {$name}Service implements {$name}ServiceInterface\n{\n    public function __construct()\n    {\n        // Inyecta dependencias si es necesario\n    }\n}\n";
        File::put("{$modulePath}/Services/{$name}Service.php", $serviceStub);
    }
}
