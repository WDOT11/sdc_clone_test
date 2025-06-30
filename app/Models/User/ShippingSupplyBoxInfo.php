<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class ShippingSupplyBoxInfo extends Model
{
    /* Specify the table name */
    protected $table = 'shipping_supply_boxes_info';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'user_id',
        'shipping_supply_id',
        'box_type',
        'box_quantity',
    ];
}
