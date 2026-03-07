<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakePackageCommand extends Command
{
    protected $signature = 'make:package {name : El nombre del paquete (Ej: Inventory)}';

    protected $description = 'Crea la estructura base de un nuevo paquete en packages/ y registra su namespace en composer.json';

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $packagePath = base_path("packages/{$name}");

        if (File::exists($packagePath)) {
            $this->error("⚠️  El paquete {$name} ya existe en packages/.");
            return Command::FAILURE;
        }

        $this->info("Creando estructura para el paquete: {$name}...");

        File::makeDirectory("{$packagePath}/Models", 0755, true);
        File::makeDirectory("{$packagePath}/Modules", 0755, true);

        $this->generateServiceProvider($name, $packagePath);
        $this->registerComposerNamespace($name);

        $this->info("✅ ¡Paquete {$name} creado con éxito!");
        $this->line("📌 Pasos siguientes:");
        $this->line("   1. Ejecuta: <comment>composer dump-autoload</comment>");
        $this->line("   2. Registra <comment>{$name}\\{$name}ServiceProvider::class</comment> en <comment>bootstrap/providers.php</comment>");
        $this->line("   3. Crea un módulo con: <comment>php artisan package:make-module {$name} NombreModulo</comment>");

        return Command::SUCCESS;
    }

    protected function generateServiceProvider(string $name, string $packagePath): void
    {
        $stub = <<<PHP
<?php

namespace {$name};

use Illuminate\Support\ServiceProvider;

class {$name}ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registra aquí los ServiceProviders de tus módulos
    }
}
PHP;

        File::put("{$packagePath}/{$name}ServiceProvider.php", $stub . "\n");
    }

    protected function registerComposerNamespace(string $name): void
    {
        $composerPath = base_path('composer.json');
        $composer = json_decode(File::get($composerPath), true);

        $namespaceKey = "{$name}\\";
        $namespacePath = "packages/{$name}/";

        if (isset($composer['autoload']['psr-4'][$namespaceKey])) {
            $this->line("  ℹ️  Namespace <comment>{$namespaceKey}</comment> ya estaba en composer.json.");
            return;
        }

        $composer['autoload']['psr-4'][$namespaceKey] = $namespacePath;

        File::put(
            $composerPath,
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n"
        );

        $this->line("  ✔ Namespace <comment>{$namespaceKey}</comment> registrado en composer.json.");
    }
}
