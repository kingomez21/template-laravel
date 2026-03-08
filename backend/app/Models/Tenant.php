<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $guarded = [];

    protected $table = 'tenants';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'domain',
        'schema',
        'owner',
        'email',
    ];
}
