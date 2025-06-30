<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ShippingOption extends Model
{
    protected $table = 'shipping_options';
    protected $fillable = [
        'name',
        'price',
        'option_for',
        'active',
        'service_provider_id',
    ];
}
