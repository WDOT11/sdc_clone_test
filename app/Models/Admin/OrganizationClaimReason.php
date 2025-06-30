<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class OrganizationClaimReason extends Model
{
    protected $fillable = [
        'org_id',
        'claim_reason_id',
        'service_provider_id',
        'active',
    ];
}
