<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;
    protected static ?string $title = '';
    protected static ?string $navigationLabel = 'Dashboard';

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
}