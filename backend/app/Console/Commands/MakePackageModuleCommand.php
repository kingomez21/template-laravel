<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakePackageModuleCommand extends Command
{
    protected $signature = 'package:make-module
                            {package : El nombre del paquete (Ej: Core)}
                            {module  : El nombre del módulo (Ej: Users)}';

    protected $description = 'Crea la estructura base de un módulo dentro de un paquete en packages/';

    public function handle(): int
    {
        $package = Str::studly($this->argument('package'));
        $module  = Str::studly($this->argument('module'));

        $packagePath = base_path("packages/{$package}");
        $modulePath  = "{$packagePath}/Modules/{$module}";

        if (!File::exists($packagePath)) {
            $this->error("❌ El paquete {$package} no existe. Créalo primero con: php artisan make:package {$package}");
            return Command::FAILURE;
        }

        if (File::exists($modulePath)) {
            $this->error("⚠️  El módulo {$module} ya existe dentro del paquete {$package}.");
            return Command::FAILURE;
        }

        $this->info("Creando módulo {$module} en el paquete {$package}...");

        // Crear carpeta Commands dentro del módulo
        File::makeDirectory("{$modulePath}/Commands", 0755, true);

        $this->generateInterface($package, $module, $modulePath);
        $this->generateService($package, $module, $modulePath);
        $this->generateController($package, $module, $modulePath);
        $this->generateRoutes($package, $module, $modulePath);
        $this->generateServiceProvider($package, $module, $modulePath);
        $this->registerInPackageProvider($package, $module, $packagePath);

        $this->info("✅ ¡Módulo {$module} creado con éxito en el paquete {$package}!");
        $this->line("📌 Archivos creados en <comment>packages/{$package}/Modules/{$module}/</comment>");

        return Command::SUCCESS;
    }

    protected function generateInterface(string $package, string $module, string $modulePath): void
    {
        $stub = <<<PHP
<?php

namespace {$package}\Modules\\{$module};

interface I{$module}
{
    // Define tus métodos aquí
}
PHP;
        File::put("{$modulePath}/I{$module}.php", $stub . "\n");
    }

    protected function generateService(string $package, string $module, string $modulePath): void
    {
        $stub = <<<PHP
<?php

namespace {$package}\Modules\\{$module};

class {$module}Service implements I{$module}
{
    public function __construct()
    {
        // Inyecta dependencias si es necesario
    }
}
PHP;
        File::put("{$modulePath}/{$module}Service.php", $stub . "\n");
    }

    protected function generateController(string $package, string $module, string $modulePath): void
    {
        $stub = <<<PHP
<?php

namespace {$package}\Modules\\{$module};

use App\Http\Controllers\Controller;

class {$module}Controller extends Controller
{
    public function __construct(protected I{$module} \$service)
    {
    }

    public function index()
    {
        return response()->json([]);
    }
}
PHP;
        File::put("{$modulePath}/{$module}Controller.php", $stub . "\n");
    }

    protected function generateRoutes(string $package, string $module, string $modulePath): void
    {
        $stub = <<<PHP
<?php

namespace {$package}\Modules\\{$module};

use {$package}\Modules\\{$module}\\{$module}Controller;
use Illuminate\Support\Facades\Route;

Route::get('/list', [{$module}Controller::class, 'index']);
PHP;
        File::put("{$modulePath}/routes.php", $stub . "\n");
    }

    protected function generateServiceProvider(string $package, string $module, string $modulePath): void
    {
        $prefix = Str::lower($module);

        $stub = <<<PHP
<?php

namespace {$package}\Modules\\{$module};

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class {$module}ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        \$this->app->bind(
            \\{$package}\Modules\\{$module}\I{$module}::class,
            \\{$package}\Modules\\{$module}\\{$module}Service::class
        );
    }

    public function boot(): void
    {
        Route::prefix('api/{$prefix}')
            ->middleware('api')
            ->group(__DIR__ . '/routes.php');
    }
}
PHP;
        File::put("{$modulePath}/{$module}ServiceProvider.php", $stub . "\n");
    }

    protected function registerInPackageProvider(string $package, string $module, string $packagePath): void
    {
        $providerPath = "{$packagePath}/{$package}ServiceProvider.php";

        if (!File::exists($providerPath)) {
            $this->warn("No se encontró {$package}ServiceProvider.php. Registra el módulo manualmente:");
            $this->line("    \$this->app->register(\\{$package}\\Modules\\{$module}\\{$module}ServiceProvider::class);");
            return;
        }

        $content = File::get($providerPath);
        $registerLine = "        \$this->app->register(\\{$package}\\Modules\\{$module}\\{$module}ServiceProvider::class);";

        if (Str::contains($content, "{$module}ServiceProvider::class")) {
            return; // Ya está registrado
        }

        // Inserta la nueva línea justo antes del cierre } del método register()
        $updated = preg_replace(
            '/(public function register\(\): void\s*\{)(.*?)(\n    \})/s',
            "$1$2\n{$registerLine}$3",
            $content,
            1
        );

        if ($updated === null || $updated === $content) {
            $this->warn("No se pudo auto-registrar el módulo. Agrega manualmente en {$package}ServiceProvider::register():");
            $this->line("    {$registerLine}");
            return;
        }

        File::put($providerPath, $updated);
        $this->line("  ✔ Módulo registrado automáticamente en <comment>{$package}ServiceProvider.php</comment>");
    }
}
