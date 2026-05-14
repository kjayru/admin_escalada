<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'paypal_order_id',
        'payer_name',
        'payer_last_name',
        'payer_email',
        'amount',
        'currency',
        'status',
        'captured_at',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'captured_at' => 'datetime',
    ];
}
