<?php

namespace App\Models;

use App\Models\BudgetSetting;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'name',
        'type',
        'currency',
        'opening_balance',
        'opening_balance_as_of_date',
        'is_default',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'opening_balance_as_of_date' => 'date',
        'is_default' => 'bool',
    ];

    /** @return HasMany<Income> */
    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    /** @return HasMany<Expense> */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function getCurrentBalancePkrAttribute(): float
    {
        $opening = BudgetSetting::toPkr((float) $this->opening_balance, $this->currency ?? 'PKR');

        $incomePkr = $this->incomes()->get()->sum(
            fn ($i) => $i->amount_in_pkr !== null
                ? (float) $i->amount_in_pkr
                : BudgetSetting::toPkr((float) $i->amount, $i->currency ?? 'PKR')
        );

        $expensePkr = $this->expenses()->get()->sum(
            fn ($e) => BudgetSetting::toPkr((float) $e->amount, $e->currency ?? 'PKR')
        );

        return $opening + $incomePkr - $expensePkr;
    }
}
