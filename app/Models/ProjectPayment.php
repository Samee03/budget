<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProjectPayment extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'project_id',
        'account_id',
        'received_at',
        'amount',
        'currency',
        'fx_rate_to_pkr',
        'amount_in_pkr',
        'method',
        'reference',
        'notes',
    ];

    protected $attributes = [
        'currency' => 'USD',
    ];

    protected $casts = [
        'received_at' => 'date',
        'amount' => 'decimal:2',
        'fx_rate_to_pkr' => 'decimal:4',
        'amount_in_pkr' => 'decimal:2',
    ];

    /** @return BelongsTo<Project, ProjectPayment> */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /** @return BelongsTo<Account, ProjectPayment> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('payment_receipts')
            ->useDisk(config('filesystems.default'));
    }
}

