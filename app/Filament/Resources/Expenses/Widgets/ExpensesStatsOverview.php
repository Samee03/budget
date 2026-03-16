<?php

namespace App\Filament\Resources\Expenses\Widgets;

use App\Models\Expense;
use App\Models\BudgetSetting;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExpensesStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $allTime = Expense::query()->get()->sum(
            fn ($e) => BudgetSetting::toPkr((float) $e->amount, $e->currency ?? 'PKR')
        );

        $thisMonth = Expense::query()
            ->whereBetween('spent_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->get()
            ->sum(
                fn ($e) => BudgetSetting::toPkr((float) $e->amount, $e->currency ?? 'PKR')
            );

        $today = Expense::query()
            ->whereDate('spent_at', now()->toDateString())
            ->get()
            ->sum(
                fn ($e) => BudgetSetting::toPkr((float) $e->amount, $e->currency ?? 'PKR')
            );

        $count = Expense::query()->count();

        return [
            Stat::make('Total expenses (all time)', 'PKR ' . number_format($allTime, 0)),
            Stat::make('This month\'s expenses', 'PKR ' . number_format((float) $thisMonth, 2)),
            Stat::make('Today\'s expenses', 'PKR ' . number_format((float) $today, 2))
                ->description($count . ' total records'),
        ];
    }
}

