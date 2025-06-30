<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class RepairReason extends Model
{
    protected $fillable = [
        'repair_reason_name',
        'active',
        'service_provider_id',
    ];
}
