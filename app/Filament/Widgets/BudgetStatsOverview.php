<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use App\Models\BudgetSetting;
use App\Models\Expense;
use App\Models\Income;
use App\Models\ProjectPayment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BudgetStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $openingBalancePkr = Account::query()->get()->sum(
            fn ($a) => BudgetSetting::toPkr((float) $a->opening_balance, $a->currency ?? 'PKR')
        );
        $allTimePaymentIncomePkr = ProjectPayment::query()->get()->sum(
            fn ($p) => $p->amount_in_pkr !== null
                ? (float) $p->amount_in_pkr
                : BudgetSetting::toPkr((float) $p->amount, $p->currency ?? 'USD')
        );
        $allTimeOtherIncomePkr = Income::query()->get()->sum(
            fn ($i) => $i->amount_in_pkr !== null
                ? (float) $i->amount_in_pkr
                : BudgetSetting::toPkr((float) $i->amount, $i->currency ?? 'PKR')
        );
        $allTimeIncomePkr = $allTimePaymentIncomePkr + $allTimeOtherIncomePkr;
        $allTimeExpensesPkr = Expense::query()->get()->sum(fn ($e) => BudgetSetting::toPkr((float) $e->amount, $e->currency ?? 'USD'));
        $currentBalancePkr = $openingBalancePkr + $allTimeIncomePkr - $allTimeExpensesPkr;

        $stats = [
            Stat::make('Opening balances (PKR)', 'PKR ' . number_format($openingBalancePkr, 0))
                ->description('Sum of account openings'),
            Stat::make('Current balance (PKR)', 'PKR ' . number_format($currentBalancePkr, 0))
                ->description('Openings + all income − all expenses'),
        ];

        return $stats;
    }
}
