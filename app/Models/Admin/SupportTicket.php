<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $table = 'support_ticket';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'subject',
        'message',
        'active',
        'service_provider_id',
        'created_at',
        'updated_at',
    ];
}
