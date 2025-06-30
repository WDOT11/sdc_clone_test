<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = [
        'route_name',
        'access_type',
        'route_type',
        'group_name',
        'service_provider_id',
    ];

    public function routePermissions()
    {
        return $this->hasMany(RoutePermission::class);
    }
}
