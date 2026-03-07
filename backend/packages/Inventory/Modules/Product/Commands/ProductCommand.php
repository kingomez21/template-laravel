<?php

namespace Inventory\Modules\Product\Commands;

use Illuminate\Console\Command;

class ProductCommand extends Command
{
    protected $signature = 'package:command-name';

    protected $description = 'Descripción del comando';

    public function handle(): void
    {
        $this->info('Comando ProductCommand ejecutado correctamente.');
    }
}
