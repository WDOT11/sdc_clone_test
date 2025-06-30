<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
    /* Table */
    protected $table = 'user_meta';
    /* Fillable Fields */
    protected $fillable = [
        'user_id',
        'meta_key',
        'meta_value',
        'active',
        'service_provider_id',
    ];
}
