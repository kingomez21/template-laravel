<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakePackageComponentCommand extends Command
{
    protected $signature = 'package:make
                            {package : El nombre del paquete (Ej: Core)}
                            {type    : Tipo de componente (model, migration, controller, service, interface, command, job, request, export, import)}
                            {name    : El nombre de la clase (Ej: ProductController)}
                            {--module= : Nombre del módulo, requerido para: controller, service, interface, command, job, request, export, import}';

    protected $description = 'Crea un componente dentro de un paquete en packages/';

    /**
     * Tipos que viven directamente en packages/{Package}/ (sin módulo).
     * Clave = tipo, Valor = subcarpeta dentro del paquete.
     */
    private const PACKAGE_LEVEL_TYPES = [
        'model'     => 'Models',
        'migration' => 'Migrations',
    ];

    /**
     * Tipos que van dentro de packages/{Package}/Modules/{Module}/
     * junto con la subcarpeta opcional donde se ubican.
     */
    private const MODULE_LEVEL_TYPES = [
        'controller' => '',
        'service'    => '',
        'interface'  => '',
        'command'    => 'Commands',
        'job'        => 'Jobs',
        'request'    => 'Requests',
        'export'     => 'Exports',
        'import'     => 'Imports',
    ];

    public function handle(): int
    {
        $package = Str::studly($this->argument('package'));
        $type    = strtolower($this->argument('type'));
        $name    = Str::studly($this->argument('name'));
        $module  = $this->option('module') ? Str::studly($this->option('module')) : null;

        $packagePath = base_path("packages/{$package}");

        if (!File::exists($packagePath)) {
            $this->error("❌ El paquete {$package} no existe. Créalo primero con: php artisan make:package {$package}");
            return Command::FAILURE;
        }

        if (!array_key_exists($type, self::PACKAGE_LEVEL_TYPES) && !array_key_exists($type, self::MODULE_LEVEL_TYPES)) {
            $allTypes = array_merge(array_keys(self::PACKAGE_LEVEL_TYPES), array_keys(self::MODULE_LEVEL_TYPES));
            $this->error("❌ Tipo no válido. Usa: " . implode(', ', $allTypes));
            return Command::FAILURE;
        }

        if (array_key_exists($type, self::PACKAGE_LEVEL_TYPES)) {
            return $this->createPackageLevelComponent($package, $type, $name, $packagePath);
        }

        if (empty($module)) {
            $this->error("❌ El tipo <comment>{$type}</comment> requiere el argumento --module=NombreModulo");
            return Command::FAILURE;
        }

        $modulePath = "{$packagePath}/Modules/{$module}";
        if (!File::exists($modulePath)) {
            $this->error("❌ El módulo {$module} no existe en el paquete {$package}. Créalo con: php artisan package:make-module {$package} {$module}");
            return Command::FAILURE;
        }

        return $this->createModuleLevelComponent($package, $module, $type, $name, $modulePath);
    }

    private function createPackageLevelComponent(string $package, string $type, string $name, string $packagePath): int
    {
        $folder    = self::PACKAGE_LEVEL_TYPES[$type];
        $basePath  = "{$packagePath}/{$folder}";
        $namespace = "{$package}\\{$folder}";

        $fileName = $type === 'migration'
            ? date('Y_m_d_His') . '_' . Str::snake($name) . '.php'
            : "{$name}.php";

        $filePath = "{$basePath}/{$fileName}";

        if (File::exists($filePath)) {
            $this->error("⚠️  El componente {$name} ya existe en {$package}/{$folder}/.");
            return Command::FAILURE;
        }

        if (!File::exists($basePath)) {
            File::makeDirectory($basePath, 0755, true);
        }

        File::put($filePath, $this->getStub($type, $namespace, $name));

        $this->info("✅ [{$type}] <comment>{$name}</comment> creado en:");
        $this->line("   packages/{$package}/{$folder}/{$fileName}");

        return Command::SUCCESS;
    }

    private function createModuleLevelComponent(
        string $package,
        string $module,
        string $type,
        string $name,
        string $modulePath
    ): int {
        $subFolder = self::MODULE_LEVEL_TYPES[$type];
        $basePath  = $subFolder ? "{$modulePath}/{$subFolder}" : $modulePath;

        // Namespace: subcarpetas como Commands, Jobs, etc. forman parte del namespace
        $namespaceBase = "{$package}\\Modules\\{$module}";
        $namespace     = $subFolder ? "{$namespaceBase}\\{$subFolder}" : $namespaceBase;

        if ($type === 'migration') {
            $fileName = date('Y_m_d_His') . '_' . Str::snake($name) . '.php';
        } elseif ($type === 'interface') {
            $fileName = "I{$name}.php";
        } else {
            $fileName = "{$name}.php";
        }

        $filePath = "{$basePath}/{$fileName}";

        if (!File::exists($basePath)) {
            File::makeDirectory($basePath, 0755, true);
        }

        if (File::exists($filePath)) {
            $this->error("⚠️  El componente ya existe en: {$filePath}");
            return Command::FAILURE;
        }

        File::put($filePath, $this->getStub($type, $namespace, $name));

        $relativePath = "packages/{$package}/Modules/{$module}" . ($subFolder ? "/{$subFolder}" : '') . "/{$fileName}";
        $this->info("✅ [{$type}] <comment>{$name}</comment> creado en:");
        $this->line("   {$relativePath}");

        return Command::SUCCESS;
    }

    protected function getStub(string $type, string $namespace, string $className): string
    {
        return match ($type) {
            'model' => <<<PHP
<?php

namespace {$namespace};

use Illuminate\Database\Eloquent\Model;

class {$className} extends Model
{
    protected \$guarded = [];
}
PHP . "\n",

            'controller' => <<<PHP
<?php

namespace {$namespace};

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class {$className} extends Controller
{
    //
}
PHP . "\n",

            'service' => <<<PHP
<?php

namespace {$namespace};

class {$className}
{
    public function __construct()
    {
        //
    }
}
PHP . "\n",

            'interface' => <<<PHP
<?php

namespace {$namespace};

interface I{$className}
{
    // Define tus métodos aquí
}
PHP . "\n",

            'command' => <<<PHP
<?php

namespace {$namespace};

use Illuminate\Console\Command;

class {$className} extends Command
{
    protected \$signature = 'package:command-name';

    protected \$description = 'Descripción del comando';

    public function handle(): void
    {
        \$this->info('Comando {$className} ejecutado correctamente.');
    }
}
PHP . "\n",

            'job' => <<<PHP
<?php

namespace {$namespace};

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class {$className} implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        //
    }
}
PHP . "\n",

            'request' => <<<PHP
<?php

namespace {$namespace};

use Illuminate\Foundation\Http\FormRequest;

class {$className} extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}
PHP . "\n",

            'export' => <<<PHP
<?php

namespace {$namespace};

use Maatwebsite\Excel\Concerns\FromCollection;

class {$className} implements FromCollection
{
    public function collection()
    {
        return collect([]);
    }
}
PHP . "\n",

            'import' => <<<PHP
<?php

namespace {$namespace};

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class {$className} implements ToCollection
{
    public function collection(Collection \$collection): void
    {
        // Procesa cada fila del Excel importado
    }
}
PHP . "\n",

            'migration' => <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('table_name', function (Blueprint \$table) {
            \$table->id();
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_name');
    }
};
PHP . "\n",

            default => "<?php\n\nnamespace {$namespace};\n\nclass {$className}\n{\n    //\n}\n",
        };
    }
}
