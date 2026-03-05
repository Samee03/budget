<?php

namespace App\Filament\Resources\Accounts;

use App\Filament\Resources\Accounts\Pages\ManageAccounts;
use App\Models\Account;
use BackedEnum;
use UnitEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|null|UnitEnum $navigationGroup = 'Budgeting';

    protected static ?int $navigationSort = 0;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('type')
                    ->options([
                        'bank' => 'Bank account',
                        'cash' => 'Cash',
                        'wallet' => 'Wallet',
                        'other' => 'Other',
                    ])
                    ->default('bank')
                    ->required(),
                Select::make('currency')
                    ->options([
                        'PKR' => 'PKR',
                        'USD' => 'USD',
                    ])
                    ->default('PKR')
                    ->required(),
                TextInput::make('opening_balance')
                    ->numeric()
                    ->default(0),
                DatePicker::make('opening_balance_as_of_date')
                    ->label('Opening balance as of'),
                Select::make('is_default')
                    ->label('Default account')
                    ->options([1 => 'Yes', 0 => 'No'])
                    ->default(0),
                Textarea::make('notes')
                    ->rows(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('type')
                    ->badge(),
                \Filament\Tables\Columns\TextColumn::make('currency'),
                \Filament\Tables\Columns\TextColumn::make('opening_balance')
                    ->label('Opening balance')
                    ->formatStateUsing(fn ($state, $record) => ($record->currency ?? 'PKR') . ' ' . number_format((float) $state, 2)),
                \Filament\Tables\Columns\TextColumn::make('current_balance_pkr')
                    ->label('Current balance (PKR)')
                    ->formatStateUsing(fn ($state) => 'PKR ' . number_format((float) $state, 0)),
                \Filament\Tables\Columns\IconColumn::make('is_default')
                    ->boolean()
                    ->label('Default'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAccounts::route('/'),
        ];
    }
}
