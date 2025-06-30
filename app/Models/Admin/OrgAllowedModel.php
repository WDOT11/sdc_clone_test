<?php

namespace App\Models\Admin;

use App\Models\Admin\DeviceModel;
use App\Models\Admin\Organization;
use Illuminate\Database\Eloquent\Model;

class OrgAllowedModel extends Model
{
    protected $table = 'org_allowed_models';

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

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }

    public function model()
    {
        return $this->belongsTo(DeviceModel::class, 'model_id');
    }
}