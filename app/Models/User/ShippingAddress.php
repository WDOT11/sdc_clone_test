<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    /* Specify the table name */
    protected $table = 'shipping_address';

    protected $fillable = [
        'id',
        'user_id',
        'claim_id',
        'repair_id',
        'device_id',
        'shipping_supply_id',
        'phone',
        'street_address',
        'address_line_2',
        'city',
        'state',
        'zip',
        'country',
        'card_number',
        'card_holder_name',
        'service_provider_id',
        'active'
    ];
}
