<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use Filament\Widgets\ChartWidget;

class AccountBalancesChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Account Balances (PKR)';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $accounts = Account::query()->orderBy('name')->get();

        $labels = [];
        $data = [];
        $palette = [
            'rgb(34, 197, 94)',   // green
            'rgb(59, 130, 246)',  // blue
            'rgb(234, 179, 8)',   // amber
            'rgb(168, 85, 247)',  // violet
            'rgb(236, 72, 153)',  // pink
            'rgb(20, 184, 166)',  // teal
        ];
        $backgroundColors = [];

        foreach ($accounts as $index => $account) {
            $balance = (float) $account->current_balance_pkr;
            $labels[] = $account->name . ' (PKR ' . number_format($balance, 0) . ')';
            $data[] = abs($balance) ?: 0.01; // use 0.01 so zero-balance accounts still show a sliver
            $backgroundColors[] = $balance < 0 ? 'rgb(239, 68, 68)' : $palette[$index % count($palette)];
        }

        if (empty($labels)) {
            $labels[] = 'No accounts';
            $data[] = 1;
            $backgroundColors = ['rgb(156, 163, 175)'];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Balance (PKR)',
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
