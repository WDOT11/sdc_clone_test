<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    /* Table */
    protected $table = 'login_logs';

    /* Disable timestamps */
    public $timestamps = false;

    /*fillable Fields */
    protected $fillable = [
        'ip_address',
        'browser',
        'id_type',
        'panel',
        'user_email',
        'user_id',
        'date',
        'success',
        'password_attempt',
    ];
    

}
