<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class IncomeType extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function booted(): void
    {
        static::saving(function (IncomeType $type): void {
            if (empty($type->slug) && ! empty($type->name)) {
                $type->slug = Str::slug($type->name);
            }
        });
    }

    /** @return HasMany<Income, IncomeType> */
    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class, 'income_type_id');
    }
}
