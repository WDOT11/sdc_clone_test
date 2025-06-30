<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'message',
        'notification_for',
        'device_id',
        'shipping_supply_id',
        'device_model_id',
        'is_claim_status_changed',
        'device_claim_id',
        'device_repair_id',
        'is_repair_status_changed',
        'org_id',
        'sub_org_id',
        'user_target_link',
        'admin_target_link',
        'is_admin',
        'active',
        'service_provider_id'
    ];
}
