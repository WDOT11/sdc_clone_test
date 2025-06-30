<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'id',
        'device_title',
        'device_model_id',
        'serial_number',
        'cellular_service',
        'imei',
        'carrier',
        'capacity',
        'color',
        'device_type',
        'org_id',
        'user_id',
        'sub_org_id',
        'org_user_first_name',
        'org_user_last_name',
        'org_user_full_name',
        'org_user_designation',
        'device_owner_name',
        'org_user_id',
        'plan_id',
        'payment_added_date',
        'expiration_date',
        'billing_cycle_id',
        'asset_tag',
        'cover_img_url',
        'is_imported_device',
        'membership_agreement',
        'is_org_subscriber_device',
        'subscription_id',
        'transaction_id',
        'active',
        'service_provider_id',
    ];
}
