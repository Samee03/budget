<?php

namespace App\Filament\Widgets;

use App\Models\BudgetSetting;
use App\Models\Expense;
use App\Models\Project;
use App\Models\Income;
use App\Models\ProjectPayment;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class BudgetStatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $startDate = isset($this->filters['startDate']) && $this->filters['startDate']
            ? Carbon::parse($this->filters['startDate'])->startOfDay()
            : null;

        $endDate = isset($this->filters['endDate']) && $this->filters['endDate']
            ? Carbon::parse($this->filters['endDate'])->endOfDay()
            : null;

        $paymentIncomeQuery = ProjectPayment::query();
        if ($startDate) {
            $paymentIncomeQuery->whereDate('received_at', '>=', $startDate);
        }
        if ($endDate) {
            $paymentIncomeQuery->whereDate('received_at', '<=', $endDate);
        }
        $paymentIncomePkr = $paymentIncomeQuery->get()->sum(
            fn ($p) => $p->amount_in_pkr !== null
                ? (float) $p->amount_in_pkr
                : BudgetSetting::toPkr((float) $p->amount, $p->currency ?? 'USD')
        );

        $otherIncomeQuery = Income::query();
        if ($startDate) {
            $otherIncomeQuery->whereDate('received_at', '>=', $startDate);
        }
        if ($endDate) {
            $otherIncomeQuery->whereDate('received_at', '<=', $endDate);
        }
        $otherIncomePkr = $otherIncomeQuery->get()->sum(
            fn ($i) => $i->amount_in_pkr !== null
                ? (float) $i->amount_in_pkr
                : BudgetSetting::toPkr((float) $i->amount, $i->currency ?? 'PKR')
        );

        $totalIncomePkr = $paymentIncomePkr + $otherIncomePkr;

        $expensesQuery = Expense::query();
        if ($startDate) {
            $expensesQuery->whereDate('spent_at', '>=', $startDate);
        }
        if ($endDate) {
            $expensesQuery->whereDate('spent_at', '<=', $endDate);
        }
        $totalExpensesPkr = $expensesQuery->get()->sum(fn ($e) => BudgetSetting::toPkr((float) $e->amount, $e->currency ?? 'USD'));

        $periodNetPkr = $totalIncomePkr - $totalExpensesPkr;

        $openingBalancePkr = BudgetSetting::openingBalanceInPkr();
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

        $settings = BudgetSetting::instance();
        $asOf = $settings->opening_balance_as_of_date?->format('M j, Y') ?? '—';

        $activeProjectsCount = Project::query()->where('status', 'active')->count();
        $ongoingProjectsCount = Project::query()
            ->where(function ($query): void {
                $query->where('is_ongoing', true)->orWhereNull('end_date')->orWhereDate('end_date', '>=', now());
            })
            ->count();

        return [
            Stat::make('Income (PKR)', 'PKR ' . number_format($totalIncomePkr, 0))
                ->description($this->getDateRangeDescription())
                ->color('success'),
            Stat::make('Expenses (PKR)', 'PKR ' . number_format($totalExpensesPkr, 0))
                ->description($this->getDateRangeDescription())
                ->color('danger'),
            Stat::make('Period net (PKR)', 'PKR ' . number_format($periodNetPkr, 0))
                ->description($this->getDateRangeDescription())
                ->color($periodNetPkr >= 0 ? 'success' : 'danger'),
            Stat::make('Opening balance (PKR)', 'PKR ' . number_format($openingBalancePkr, 0))
                ->description('As of ' . $asOf),
            Stat::make('Current balance (PKR)', 'PKR ' . number_format($currentBalancePkr, 0))
                ->description('Opening + income − expenses'),
            Stat::make('Active projects', (string) $activeProjectsCount)
                ->description('Status = active'),
            Stat::make('Ongoing projects', (string) $ongoingProjectsCount)
                ->description('Not finished yet'),
        ];
    }

    protected function getDateRangeDescription(): string
    {
        $start = $this->filters['startDate'] ?? null;
        $end = $this->filters['endDate'] ?? null;

        if (! $start && ! $end) {
            return 'All time';
        }
        if ($start && $end) {
            return sprintf('From %s to %s', $start, $end);
        }
        if ($start) {
            return sprintf('From %s', $start);
        }
        return sprintf('Until %s', $end);
    }
}
