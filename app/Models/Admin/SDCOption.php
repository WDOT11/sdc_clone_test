<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class SDCOption extends Model
{
    protected $table = 'sdc_options';
    protected $fillable = [
        'option_key',
        'option_value',
        'active',
        'service_provider_id',
    ];
}
