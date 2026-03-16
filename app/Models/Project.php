<?php

namespace App\Models;

use App\Models\Expense;
use App\Models\Income;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'name',
        'description',
        'total_amount',
        'currency',
        'start_date',
        'end_date',
        'is_ongoing',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_ongoing' => 'bool',
        'total_amount' => 'decimal:2',
    ];

    /** @return BelongsTo<User, Project> */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /** @return HasMany<Income> */
    public function payments(): HasMany
    {
        return $this->hasMany(Income::class)
            ->where('income_kind', 'project_payment');
    }

    /** @return HasMany<Expense> */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0.0, (float) $this->total_amount - $this->total_paid);
    }

    public function getTotalExpensesAttribute(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    public function getProfitAttribute(): float
    {
        return (float) $this->total_paid - $this->total_expenses;
    }
}

