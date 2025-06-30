<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /* Table */
    protected $table = 'transactions';
    /*fillable Fields */
    protected $fillable = [
        'user_id',
        'stripe_customer_id',
        'stripe_transaction_id',
        'amount',
        'currency',
        'payment_method',
        'status',
        'payment_for',
        'device_id',
        'device_claim_id',
        'device_repair_id',
        'invoice_id',
        'description',
        'service_provider_id',
    ];
}
