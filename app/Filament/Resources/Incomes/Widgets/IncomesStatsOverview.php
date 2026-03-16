<?php

namespace App\Filament\Resources\Incomes\Widgets;

use App\Models\Income;
use App\Models\BudgetSetting;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class IncomesStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $allTime = Income::query()->get()->sum(
            fn ($i) => $i->amount_in_pkr !== null
                ? (float) $i->amount_in_pkr
                : BudgetSetting::toPkr((float) $i->amount, $i->currency ?? 'PKR')
        );

        $thisMonth = Income::query()
            ->whereBetween('received_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->get()
            ->sum(
                fn ($i) => $i->amount_in_pkr !== null
                    ? (float) $i->amount_in_pkr
                    : BudgetSetting::toPkr((float) $i->amount, $i->currency ?? 'PKR')
            );
        $today = Income::query()
            ->whereDate('received_at', now()->toDateString())
            ->get()
            ->sum(
                fn ($i) => $i->amount_in_pkr !== null
                    ? (float) $i->amount_in_pkr
                    : BudgetSetting::toPkr((float) $i->amount, $i->currency ?? 'PKR')
            );
        $count = Income::query()->count();

        return [
            Stat::make('Total income (all time)', 'PKR ' . number_format($allTime, 0)),
            Stat::make('This month\'s income', 'PKR ' . number_format((float) $thisMonth, 2)),
            Stat::make('Today\'s income', 'PKR ' . number_format((float) $today, 2))
                ->description($count . ' total records'),
        ];
    }
}

