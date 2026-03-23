<?php

namespace App\Filament\Widgets;

use App\Models\BudgetSetting;
use App\Models\Expense;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ExpensesByCategoryDoughnutChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Expenses by Category (PKR)';

    protected ?string $maxHeight = '300px';

    // This drives the header filter dropdown rendered by Filament's `ChartWidget`.
    public ?string $filter = 'current_month';

    protected function getFilters(): ?array
    {
        return [
            'current_month' => 'Current month',
            'last_3_months' => 'Last 3 months',
            'last_6_months' => 'Last 6 months',
            'last_12_months' => 'Last 12 months',
            'current_year' => 'Current year',
        ];
    }

    protected function getRange(): array
    {
        $now = now();

        return match ($this->filter) {
            'last_3_months' => [
                $now->copy()->subMonths(2)->startOfMonth(),
                $now->copy()->endOfMonth(),
            ],
            'last_6_months' => [
                $now->copy()->subMonths(5)->startOfMonth(),
                $now->copy()->endOfMonth(),
            ],
            'last_12_months' => [
                $now->copy()->subMonths(11)->startOfMonth(),
                $now->copy()->endOfMonth(),
            ],
            'current_year' => [
                $now->copy()->startOfYear(),
                $now->copy()->endOfMonth(),
            ],
            default => [
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth(),
            ],
        };
    }

    protected function getData(): array
    {
        [$startDate, $endDate] = $this->getRange();

        $totals = [];

        $expenses = Expense::query()
            ->with('expenseCategory:id,name')
            ->whereDate('spent_at', '>=', $startDate)
            ->whereDate('spent_at', '<=', $endDate)
            ->get(['amount', 'currency', 'expense_category_id']);

        foreach ($expenses as $expense) {
            $categoryName = $expense->expenseCategory?->name ?? 'Uncategorized';
            $totals[$categoryName] = ($totals[$categoryName] ?? 0) + BudgetSetting::toPkr(
                (float) $expense->amount,
                $expense->currency ?? 'USD'
            );
        }

        arsort($totals);

        // Keep the chart readable: top 10 categories, everything else grouped into "Other".
        $top = array_slice($totals, 0, 10, true);
        $otherTotal = array_sum(array_slice($totals, 10, null, true));
        if ($otherTotal > 0) {
            $top['Other'] = $otherTotal;
        }

        $palette = [
            'rgb(34, 197, 94)',   // green
            'rgb(59, 130, 246)',  // blue
            'rgb(234, 179, 8)',   // amber
            'rgb(168, 85, 247)',  // violet
            'rgb(236, 72, 153)',  // pink
            'rgb(20, 184, 166)',  // teal
            'rgb(99, 102, 241)',  // indigo
            'rgb(244, 63, 94)',   // rose
            'rgb(45, 212, 191)',  // aqua
            'rgb(251, 146, 60)',  // orange
        ];

        $labels = [];
        $data = [];
        $backgroundColors = [];

        if (empty($top)) {
            $labels[] = 'No expenses';
            $data[] = 1;
            $backgroundColors[] = 'rgb(156, 163, 175)';
        } else {
            foreach ($top as $category => $pkrAmount) {
                $labels[] = $category;
                $data[] = $pkrAmount ?: 0.01; // keep doughnut from disappearing on zeros
                $backgroundColors[] = $palette[count($backgroundColors) % count($palette)];
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Expenses (PKR)',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderWidth' => 2,
                    'borderColor' => 'rgb(255, 255, 255)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

