<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetSetting extends Model
{
    protected $fillable = [
        'usd_to_pkr_rate',
        'opening_balance',
        'opening_balance_currency',
        'opening_balance_as_of_date',
    ];

    protected $casts = [
        'usd_to_pkr_rate' => 'decimal:4',
        'opening_balance' => 'decimal:2',
        'opening_balance_as_of_date' => 'date',
    ];

    /** Get the singleton budget settings row (id = 1). */
    public static function instance(): self
    {
        $row = static::first();
        if ($row) {
            return $row;
        }
        return static::create([
            'usd_to_pkr_rate' => 278.0,
            'opening_balance' => 0,
            'opening_balance_currency' => 'PKR',
            'opening_balance_as_of_date' => now(),
        ]);
    }

    /** Convert amount to PKR. If currency is already PKR, return as-is; if USD, multiply by rate. */
    public static function toPkr(float $amount, string $currency): float
    {
        $currency = strtoupper($currency ?: 'USD');
        if ($currency === 'PKR') {
            return $amount;
        }
        $settings = static::instance();
        $rate = (float) $settings->usd_to_pkr_rate;
        return $rate > 0 ? $amount * $rate : $amount;
    }

    /** Opening balance in PKR (convert if stored in USD). */
    public static function openingBalanceInPkr(): float
    {
        $s = static::instance();
        return (float) static::toPkr((float) $s->opening_balance, $s->opening_balance_currency ?? 'PKR');
    }
}
