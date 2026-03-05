<?php

namespace App\Filament\Pages;

use App\Models\BudgetSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use UnitEnum;

class BudgetSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string|UnitEnum|null $navigationGroup = 'Budgeting';
    protected static ?int $navigationSort = 10;
    protected static ?string $title = 'Budget Settings';
    protected static ?string $navigationLabel = 'Budget Settings';
    protected static ?string $slug = 'budget-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = BudgetSetting::instance();
        $this->data = [
            'usd_to_pkr_rate' => (string) ($settings->usd_to_pkr_rate ?? '278'),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Exchange rate')
                    ->description('Used to convert USD to PKR on the dashboard.')
                    ->schema([
                        TextInput::make('usd_to_pkr_rate')
                            ->label('1 USD = ? PKR')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->required(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save settings')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->data ?? [];
        $settings = BudgetSetting::instance();
        $settings->update([
            'usd_to_pkr_rate' => $data['usd_to_pkr_rate'] ?? null,
        ]);
        Notification::make()->title('Settings saved.')->success()->send();
        $this->redirect(static::getUrl());
    }
}
