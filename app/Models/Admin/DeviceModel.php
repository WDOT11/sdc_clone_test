<?php

namespace App\Models\Admin;

use App\Models\Admin\DeviceBrand;
use App\Models\Admin\DeviceFamily;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DeviceModel extends Model
{
    protected $fillable = [
        'title',
        'device_family_id',
        'device_brand_id',
        'show_device_model',
        'active',
        'service_provider_id'
    ];

    /* Relationships */
    public function family()
    {
        $user = Auth::user();
        return $this->belongsTo(DeviceFamily::class,'device_family_id','id')->where('active', 1)->where('service_provider_id', $user->service_provider_id);
    }
    public function brand()
    {
        $user = Auth::user();
        return $this->belongsTo(DeviceBrand::class,'device_brand_id','id')->where('active', 1)->where('service_provider_id', $user->service_provider_id);
    }
}
