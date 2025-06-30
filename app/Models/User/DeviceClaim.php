<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class DeviceClaim extends Model
{
    protected $fillable = [
        'zoho_claim_id',
        'zoho_ticket_number',
        'transaction_id',
        'user_id',
        'device_id',
        'claim_reason_id',
        'claim_details',
        'claim_amount',
        'shipping_option',
        'org_id',
        'sub_org_id',
        'claim_status',
        'user_claim_status',
        'is_org_subsciber_claim',
        'first_name',
        'last_name',
        'email',
        'phone',
        'street_address',
        'address_line_2',
        'city',
        'state',
        'zip',
        'country',
        'claim_notes',
        'status_updated_date',
        'service_provider_id',
        'active',
    ];
}
