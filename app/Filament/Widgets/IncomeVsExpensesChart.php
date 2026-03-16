<?php

namespace App\Filament\Widgets;

use App\Models\BudgetSetting;
use App\Models\Expense;
use App\Models\Income;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class IncomeVsExpensesChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected ?string $heading = 'Income vs Expenses (PKR)';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $startDate = isset($this->filters['startDate']) && $this->filters['startDate']
            ? Carbon::parse($this->filters['startDate'])->startOfMonth()
            : now()->subMonths(11)->startOfMonth();

        $endDate = isset($this->filters['endDate']) && $this->filters['endDate']
            ? Carbon::parse($this->filters['endDate'])->endOfMonth()
            : now()->endOfMonth();

        $labels = [];
        $incomeData = [];
        $expenseData = [];

        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $labels[] = $current->format('M Y');
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            $incomePkr = Income::query()
                ->whereDate('received_at', '>=', $monthStart)
                ->whereDate('received_at', '<=', $monthEnd)
                ->get()
                ->sum(fn ($i) => $i->amount_in_pkr !== null
                    ? (float) $i->amount_in_pkr
                    : BudgetSetting::toPkr((float) $i->amount, $i->currency ?? 'PKR'));
            $incomeData[] = round($incomePkr, 0);

            $expensePkr = Expense::query()
                ->whereDate('spent_at', '>=', $monthStart)
                ->whereDate('spent_at', '<=', $monthEnd)
                ->get()
                ->sum(fn ($e) => BudgetSetting::toPkr((float) $e->amount, $e->currency ?? 'USD'));
            $expenseData[] = round($expensePkr, 0);

            $current->addMonth();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => $incomeData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
                [
                    'label' => 'Expenses',
                    'data' => $expenseData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
