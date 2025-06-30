<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ClaimRepairStatus extends Model
{
    protected $table = 'claim_repair_status';

    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
    ];
}
