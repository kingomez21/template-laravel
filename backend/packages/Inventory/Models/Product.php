<?php

namespace Inventory\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
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
