<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DevicePlan extends Model
{
    protected $fillable = [
        'device_model_id',
        'plan_type',
        'plan_name',
        'price',
        'deductible_price',
        'expiration_days',
        'freq_occurence',
        'insured_uninsured_devices',
        'stripe_product_id',
        'stripe_price_id',
        'active',
        'service_provider_id',
    ];
}
