<?php

namespace App\Filament\Resources\Expenses\Schemas;

use App\Models\Project;
use App\Services\FilamentComponentService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpan(2)
                    ->schema([
                        Group::make()
                            ->schema([
                                DatePicker::make('spent_at')
                                    ->default(now())
                                    ->required(),
                                TextInput::make('amount')
                                    ->numeric()
                                    ->required(),
                                Select::make('currency')
                                    ->options(['USD' => 'USD', 'PKR' => 'PKR'])
                                    ->default('PKR')
                                    ->required(),
                                Select::make('category')
                                    ->options([
                                        'groceries' => 'Groceries',
                                        'online_shopping' => 'Online shopping',
                                        'atm_withdrawal' => 'ATM withdrawal',
                                        'outsourcing' => 'Outsourcing / Contractors',
                                        'tools' => 'Tools & Software',
                                        'travel' => 'Travel',
                                        'other' => 'Other',
                                    ])
                                    ->searchable(),
                                TextInput::make('payee_name')
                                    ->label('Payee')
                                    ->maxLength(255),
                                Textarea::make('description')
                                    ->rows(2)
                                    ->required(),
                                Select::make('payment_method')
                                    ->options([
                                        'bank_transfer' => 'Bank transfer',
                                        'cash' => 'Cash',
                                        'card' => 'Card',
                                        'wallet' => 'Wallet',
                                        'other' => 'Other',
                                    ])
                                    ->searchable(),
                                Select::make('account_id')
                                    ->label('Account (optional)')
                                    ->relationship('account', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('project_id')
                                    ->label('Project (optional)')
                                    ->relationship('project', 'name', modifyQueryUsing: fn (Builder $query) => $query->orderBy('name'))
                                    ->searchable()
                                    ->preload(),
                                FilamentComponentService::getMediaComponents('expense_receipts'),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }
}

