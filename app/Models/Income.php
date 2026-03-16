<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\IncomeType;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $fillable = [
        'account_id',
        'project_id',
        'income_type_id',
        'received_at',
        'amount',
        'currency',
        'fx_rate_to_pkr',
        'amount_in_pkr',
        'source',
        'description',
        'payment_method',
        'payment_reference',
        'income_kind',
        'notes',
    ];

    protected $attributes = [
        'currency' => 'PKR',
    ];

    protected $casts = [
        'received_at' => 'date',
        'amount' => 'decimal:2',
        'fx_rate_to_pkr' => 'decimal:4',
        'amount_in_pkr' => 'decimal:2',
    ];

    /** @return BelongsTo<Account, Income> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /** @return BelongsTo<Project, Income> */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /** @return BelongsTo<IncomeType, Income> */
    public function incomeType(): BelongsTo
    {
        return $this->belongsTo(IncomeType::class, 'income_type_id');
    }
}
