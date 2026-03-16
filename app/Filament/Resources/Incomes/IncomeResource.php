<?php

namespace App\Filament\Resources\Incomes;

use App\Filament\Resources\Incomes\Pages\ManageIncomes;
use App\Models\Income;
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

class IncomeResource extends Resource
{
    protected static ?string $model = Income::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|null|UnitEnum $navigationGroup = 'Budgeting';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('received_at')
                    ->default(now())
                    ->required(),
                Select::make('account_id')
                    ->relationship('account', 'name')
                    ->label('Account')
                    ->searchable()
                    ->preload(),
                TextInput::make('amount')
                    ->numeric()
                    ->required(),
                Select::make('currency')
                    ->options([
                        'PKR' => 'PKR',
                        'USD' => 'USD',
                    ])
                    ->default('PKR')
                    ->required(),
                TextInput::make('fx_rate_to_pkr')
                    ->numeric()
                    ->label('Rate to PKR')
                    ->helperText('If USD, set 1 unit = ? PKR. For PKR you can leave this empty.')
                    ->visible(fn ($get) => ($get('currency') ?? 'PKR') === 'USD'),
                TextInput::make('amount_in_pkr')
                    ->numeric()
                    ->label('Amount in PKR')
                    ->helperText('Optional. If filled, dashboard uses this exact PKR value.')
                    ->default(function ($get) {
                        $amount = (float) ($get('amount') ?? 0);
                        $currency = $get('currency') ?? 'PKR';
                        $rate = (float) ($get('fx_rate_to_pkr') ?? 0);

                        if ($currency === 'PKR') {
                            return $amount ?: null;
                        }

                        if ($currency === 'USD' && $amount && $rate) {
                            return $amount * $rate;
                        }

                        return null;
                    }),
                Select::make('income_type_id')
                    ->label('Type')
                    ->relationship('incomeType', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->default(fn () => \App\Models\IncomeType::where('slug', 'salary')->value('id'))
                    ->createOptionForm([
                        \Filament\Forms\Components\TextInput::make('name')->required()->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('slug')->maxLength(255)->helperText('Leave blank to auto-generate'),
                    ]),
                TextInput::make('source')
                    ->label('Source (employer / client)')
                    ->maxLength(255),
                Textarea::make('description')
                    ->rows(2),
                Select::make('payment_method')
                    ->options([
                        'bank_transfer' => 'Bank transfer',
                        'cash' => 'Cash',
                        'card' => 'Card',
                        'wallet' => 'Wallet',
                        'other' => 'Other',
                    ]),
                Textarea::make('notes')
                    ->rows(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('received_at')
                    ->date()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('incomeType.name')
                    ->badge()
                    ->label('Type')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('source')
                    ->label('Source')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('account.name')
                    ->label('Account')
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state, $record) => ($record->currency ?? 'PKR') . ' ' . number_format((float) $state, 2)),
                \Filament\Tables\Columns\TextColumn::make('currency')
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('description')
                    ->limit(40)
                    ->wrap()
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => ManageIncomes::route('/'),
        ];
    }
}
