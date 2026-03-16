<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('expense_receipts')
            ->useDisk(config('filesystems.default'));
    }

    protected $fillable = [
        'project_id',
        'account_id',
        'expense_category_id',
        'spent_at',
        'amount',
        'currency',
        'payee_name',
        'description',
        'payment_method',
        'is_recurring',
        'notes',
    ];

    protected $attributes = [
        'currency' => 'USD',
    ];

    protected $casts = [
        'spent_at' => 'date',
        'amount' => 'decimal:2',
        'is_recurring' => 'bool',
    ];

    /** @return BelongsTo<Project, Expense> */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /** @return BelongsTo<Account, Expense> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /** @return BelongsTo<ExpenseCategory, Expense> */
    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
}

