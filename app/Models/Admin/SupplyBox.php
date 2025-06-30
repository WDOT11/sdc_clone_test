<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class SupplyBox extends Model
{
    protected $table = 'supply_boxes';
    protected $fillable = [
        'name',
        'active',
        'service_provider_id',
    ];
}
