<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantService extends Model
{
    protected $primaryKey = 'id';
    protected $table = "tenant_services";
    protected $connection = 'pgsql';

    protected $fillable = [
        'tenant_id',
        'name',
        'config',
        'type',
        'is_active',
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
            'is_active' => 'boolean'
        ];
    }

    // relations tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }
}
