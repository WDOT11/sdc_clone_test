<?php

namespace App\Models\Admin;

use App\Models\Admin\Organization;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SubOrganization extends Model
{
    protected $fillable = [
        'name',
        'org_id',
        'service_provider_id',
        'active',
    ];

    public function organization()
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        return $this->belongsTo(Organization::class, 'org_id')->where('active', 1)->where('service_provider_id', $serviceProvider);
    }
}
