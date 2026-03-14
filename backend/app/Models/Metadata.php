<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Metadata extends Model
{
    protected $primaryKey = 'id';
    protected $table = "metadata";
    protected $connection = 'pgsql';

    protected $fillable = [
        'tenant',
        'entity',
        'field',
        'name',
        'value',
        'type',
        'icon',
        'color',
        'notes',
        'config',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'config' => 'array'
        ];
    }

}
