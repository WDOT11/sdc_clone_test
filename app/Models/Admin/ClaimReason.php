<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ClaimReason extends Model
{
    protected $fillable = [
        'claim_reason_name',
        'active',
        'service_provider_id',
    ];
}