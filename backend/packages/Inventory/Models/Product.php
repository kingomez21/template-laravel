<?php

namespace Inventory\Models;

use App\Util\TenantModel;

class Product extends TenantModel
{
    protected $guarded = [];

    protected $table = 'products';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'price',
        'description',
        'in_stock',
    ];
}
