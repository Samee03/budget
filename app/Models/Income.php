<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $fillable = [
        'received_at',
        'amount',
        'currency',
        'fx_rate_to_pkr',
        'amount_in_pkr',
        'type',
        'source',
        'description',
        'payment_method',
        'notes',
    ];

    protected $attributes = [
        'currency' => 'PKR',
        'type' => 'other',
    ];

    protected $casts = [
        'received_at' => 'date',
        'amount' => 'decimal:2',
        'fx_rate_to_pkr' => 'decimal:4',
        'amount_in_pkr' => 'decimal:2',
    ];
}
