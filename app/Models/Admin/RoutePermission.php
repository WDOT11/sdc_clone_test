<?php

namespace App\Models\Admin;

use App\Models\Admin\Route;
use App\Models\Admin\Role;
use Illuminate\Database\Eloquent\Model;

class RoutePermission extends Model
{
    protected $fillable = [
        'route_id',
        'role_id',
        'service_provider_id',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
}
