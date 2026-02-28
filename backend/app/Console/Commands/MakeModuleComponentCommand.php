<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleComponentCommand extends Command
{
    protected $signature = 'module:make
                            {module : El nombre del módulo (Ej: Inventario)}
                            {type : El tipo (model, controller, service, job, request, export)}
                            {name : El nombre de la clase (Ej: ProductoController)}';

    protected $description = 'Crea un componente para un módulo en la carpeta raíz /Modules (Fuera de app/)';

    public function handle()
    {
        $module = ucfirst($this->argument('module'));
        $type = strtolower($this->argument('type'));
        $name = ucfirst($this->argument('name'));

        $folders = [
            'model'      => 'Models',
            'controller' => 'Controllers',
            'service'    => 'Services',
            'job'        => 'Jobs',
            'request'    => 'Requests',
            'export'     => 'Exports',
            'import'     => 'Imports',
            'migration'   => 'Migrations',
            'command'    => 'Console/Commands',
        ];

        if (!array_key_exists($type, $folders)) {
            $this->error("❌ Tipo no válido. Usa: " . implode(', ', array_keys($folders)));
            return Command::FAILURE;
        }

        $folderName = $folders[$type];

        $basePath = base_path("Modules/{$module}/{$folderName}");

        // 👈 Lógica especial para el nombre del archivo
        if ($type === 'migration') {
            // Ejemplo: 2026_02_27_234300_create_productos_table.php
            $fileName = date('Y_m_d_His') . '_' . Str::snake($name) . '.php';
        } else {
            $fileName = "{$name}.php";
        }

        $filePath = "{$basePath}/{$fileName}";

        if (File::exists($filePath)) {
            $this->error("⚠️ El componente {$name} ya existe en el módulo {$module}.");
            return Command::FAILURE;
        }

        if (!File::exists($basePath)) {
            File::makeDirectory($basePath, 0755, true, true);
        }

        // 2. LA CLAVE: El Namespace ya no empieza con "App\"
        $folderNamespace = str_replace('/', '\\', $folderName);
        $namespace = "Modules\\{$module}\\{$folderNamespace}";

        $content = $this->getStub($type, $namespace, $name);

        File::put($filePath, $content);

        $this->info("✅ [{$type}] creado exitosamente fuera de app/ en:");
        $this->line($filePath);

        return Command::SUCCESS;
    }

    protected function getStub($type, $namespace, $className)
    {
        $stubs = [
            'controller' => "<?php\n\nnamespace {$namespace};\n\nuse App\\Http\\Controllers\\Controller;\nuse Illuminate\\Http\\Request;\n\nclass {$className} extends Controller\n{\n    //\n}\n",
            'model' => "<?php\n\nnamespace {$namespace};\n\nuse Illuminate\\Database\\Eloquent\\Model;\n\nclass {$className} extends Model\n{\n    protected \$guarded = [];\n}\n",
            'service' => "<?php\n\nnamespace {$namespace};\n\nclass {$className}\n{\n    public function __construct()\n    {\n        //\n    }\n}\n",
            'job' => "<?php\n\nnamespace {$namespace};\n\nuse Illuminate\\Bus\\Queueable;\nuse Illuminate\\Contracts\\Queue\\ShouldQueue;\nuse Illuminate\\Foundation\\Bus\\Dispatchable;\nuse Illuminate\\Queue\\InteractsWithQueue;\nuse Illuminate\\Queue\\SerializesModels;\n\nclass {$className} implements ShouldQueue\n{\n    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;\n\n    public function handle()\n    {\n        //\n    }\n}\n",
            'request' => "<?php\n\nnamespace {$namespace};\n\nuse Illuminate\\Foundation\\Http\\FormRequest;\n\nclass {$className} extends FormRequest\n{\n    public function authorize()\n    {\n        return true;\n    }\n\n    public function rules()\n    {\n        return [\n            //\n        ];\n    }\n}\n",
            'export' => "<?php\n\nnamespace {$namespace};\n\nuse Maatwebsite\\Excel\\Concerns\\FromCollection;\n\nclass {$className} implements FromCollection\n{\n    public function collection()\n    {\n        return collect([]);\n    }\n}\n",
            'command' => "<?php\n\nnamespace {$namespace};\n\nuse Illuminate\\Console\\Command;\n\nclass {$className} extends Command\n{\n    /**\n     * The name and signature of the console command.\n     *\n     * @var string\n     */\n    protected \$signature = '" . strtolower("module") . ":" . strtolower("name") . "';\n\n    /**\n     * The console command description.\n     *\n     * @var string\n     */\n    protected \$description = 'Descripción de tu comando modular';\n\n    /**\n     * Execute the console command.\n     */\n    public function handle()\n    {\n        // Lógica de tu comando o tarea programada aquí\n        \$this->info('Comando {$className} ejecutado correctamente.');\n    }\n}\n",
            'import' => "<?php\n\nnamespace {$namespace};\n\nuse Illuminate\\Support\\Collection;\nuse Maatwebsite\\Excel\\Concerns\\ToCollection;\n\nclass {$className} implements ToCollection\n{\n    /**\n    * @param Collection \$collection\n    */\n    public function collection(Collection \$collection)\n    {\n        // Aquí procesas cada fila del Excel leído\n    }\n}\n",
            'migration' => "<?php\n\nuse Illuminate\\Database\\Migrations\\Migration;\nuse Illuminate\\Database\\Schema\\Blueprint;\nuse Illuminate\\Support\\Facades\\Schema;\n\nreturn new class extends Migration\n{\n    public function up()\n    {\n        // Cambia 'table_name' por el nombre de tu tabla\n        Schema::create('table_name', function (Blueprint \$table) {\n            \$table->id();\n            \$table->timestamps();\n        });\n    }\n\n    public function down()\n    {\n        Schema::dropIfExists('table_name');\n    }\n};\n",
        ];


        return $stubs[$type];
    }
}
