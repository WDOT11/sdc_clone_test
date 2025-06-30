<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class OrgAllowedRenewalModel extends Model
{
    protected $fillable = [
        'org_id',
        'model_id',
        'device_plan_id',
        'coverage_price',
        'deductible',
        'expiration_date',
        'stripe_product_id',
        'stripe_price_id',
        'service_provider_id',
        'active',
    ];
}
