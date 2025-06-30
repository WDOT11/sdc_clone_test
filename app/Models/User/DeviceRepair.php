<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class DeviceRepair extends Model
{
    protected $fillable = [
        'user_id',
        'zoho_repair_id',
        'zoho_ticket_number',
        'transaction_id',
        'org_id',
        'sub_org_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'device_model_id',
        'device_serial_number',
        'repair_reason_id',
        'repair_details',
        'repair_status',
        'user_repair_status',
        'repair_amount',
        'shipping_option',
        'street_address',
        'address_line_2',
        'city',
        'state',
        'zip',
        'country',
        'status_updated_date',
        'service_provider_id',
        'active',
    ];
}
