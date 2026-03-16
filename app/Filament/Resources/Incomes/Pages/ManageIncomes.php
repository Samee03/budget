<?php

namespace App\Filament\Resources\Incomes\Pages;

use App\Filament\Resources\Incomes\IncomeResource;
use App\Filament\Resources\Incomes\Widgets\IncomesStatsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageIncomes extends ManageRecords
{
    protected static string $resource = IncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            IncomesStatsOverview::class,
        ];
    }
}
