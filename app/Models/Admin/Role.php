<?php

namespace App\Models\Admin;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'role_type',
        'role_for',
        'service_provider_id',
    ];

    public function routePermissions()
    {
        return $this->hasMany(RoutePermission::class);
    }
}
