<?php

namespace Inventory\Modules\Product\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductImport implements ToCollection
{
    public function collection(Collection $collection): void
    {
        // Procesa cada fila del Excel importado
    }
}
