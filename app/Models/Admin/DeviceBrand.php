<?php

namespace App\Models\Admin;

use App\Models\Admin\DeviceFamily;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DeviceBrand extends Model
{
    protected $fillable = [
        'name',
        'device_family_id',
        'active',
        'service_provider_id'
    ];

    /* Relationships */
    public function family()
    {
        $user = Auth::user();
        return $this->belongsTo(DeviceFamily::class,'device_family_id','id')->where('active', 1)->where('service_provider_id', $user->service_provider_id);
    }
}
