<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DeviceFamily extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'family_parent_id',
        'description',
        'media_id',
        'active',
        'service_provider_id'
    ];
}
