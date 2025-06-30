<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrgRelationship extends Model
{
    /* Table */
    protected $table = 'org_relationship';
    /*fillable Fields */
    protected $fillable = [
        'user_id',
        'org_id',
        'parent_org_id',
        'is_org_subscriber',
        'active',
        'service_provider_id',
    ];
}
