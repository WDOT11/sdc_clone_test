<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class MediaLibraries extends Model
{
    protected $fillable = [
        'file_name',
        'file_path',
        'file_type',
        'active',
        'service_provider_id',
    ];

}
