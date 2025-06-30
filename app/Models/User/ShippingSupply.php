<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class ShippingSupply extends Model
{
    /* Specify the table name */
    protected $table = 'shipping_supplies';

    protected $fillable = [
        'id',
        'user_id',
        'org_id',
        'sub_org_id',
        'status',
        'street_address',
        'address_line_2',
        'country',
        'state',
        'city',
        'zipcode',
        'service_provider_id',
        'active'
    ];
}
