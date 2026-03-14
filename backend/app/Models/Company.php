<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    protected $primaryKey = 'id';
    protected $table = "companies";
    protected $connection = 'pgsql';

    protected $fillable = [
        'name',
        'nit',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'ceo_name',
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

    public function tenants()
    {
        return $this->hasMany(Tenant::class, 'company_id', 'id');
    }
}
