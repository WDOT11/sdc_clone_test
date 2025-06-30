<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{
    protected $fillable = [
        'domain_name',
        'name',
        'email',
        'phone_number',
        'address',
        'active',
    ];
}
