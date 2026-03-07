<?php

namespace Inventory\Modules\Product\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class ProductExport implements FromCollection
{
    public function collection()
    {
        return collect([]);
    }
}
