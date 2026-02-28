<?php

namespace Modules\Inventario\Console\Commands;

use Illuminate\Console\Command;

class ProductoSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'producto:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza los productos del módulo Inventario';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Lógica de tu comando o tarea programada aquí
        $this->info('Comando ProductoSync ejecutado correctamente.');
    }
}
