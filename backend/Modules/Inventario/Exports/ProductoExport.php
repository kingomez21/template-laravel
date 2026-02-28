<?php

namespace Modules\Inventario\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class ProductoExport implements FromCollection
{
    public function collection()
    {
        return collect([]);
    }
}
