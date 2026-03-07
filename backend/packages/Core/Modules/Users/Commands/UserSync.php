<?php

namespace Core\Modules\Users\Commands;

use Illuminate\Console\Command;

class UserSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza los usuarios del módulo Users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Lógica de tu comando o tarea programada aquí
        $this->info('Comando UserSync ejecutado correctamente.');
    }
}
