<?php

namespace App\Filament\RelationManagers;

use App\Services\FilamentComponentService;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectPaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Payments';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('received_at')
                ->default(now())
                ->required(),
            TextInput::make('amount')
                ->numeric()
                ->default(fn ($record) => $record?->amount ?? $this->getOwnerRecord()?->payments()->latest('received_at')->value('amount'))
                ->required(),
            Select::make('currency')
                ->options(['USD' => 'USD', 'PKR' => 'PKR'])
                ->default(fn ($get) => $this->getOwnerRecord()?->currency ?? 'USD')
                ->required(),
            Select::make('account_id')
                ->label('Account')
                ->relationship('account', 'name', modifyQueryUsing: fn (Builder $query) => $query->orderBy('name'))
                ->searchable()
                ->preload(),
            TextInput::make('fx_rate_to_pkr')
                ->numeric()
                ->label('Rate to PKR')
                ->helperText('If this is USD, set 1 unit = ? PKR. If PKR, you can leave this empty or 1.')
                ->visible(fn ($get) => ($get('currency') ?? 'USD') === 'USD'),
            TextInput::make('amount_in_pkr')
                ->numeric()
                ->label('Amount in PKR')
                ->helperText('Optional. If set, dashboard will use this exact PKR amount.')
                ->default(function ($get) {
                    $amount = (float) ($get('amount') ?? 0);
                    $currency = $get('currency') ?? 'USD';
                    $rate = (float) ($get('fx_rate_to_pkr') ?? 0);

                    if ($currency === 'PKR') {
                        return $amount ?: null;
                    }

                    if ($currency === 'USD' && $amount && $rate) {
                        return $amount * $rate;
                    }

                    return null;
                }),
            Select::make('method')
                ->options([
                    'bank_transfer' => 'Bank transfer',
                    'cash' => 'Cash',
                    'card' => 'Card',
                    'paypal' => 'PayPal',
                    'other' => 'Other',
                ])
                ->searchable(),
            TextInput::make('reference')
                ->maxLength(255),
            TextInput::make('notes')
                ->maxLength(255),
            FilamentComponentService::getMediaComponents('payment_receipts'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('received_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn ($state, $record) => ($record->currency ?? 'USD') . ' ' . number_format((float) $state, 2))
                    ->label('Amount'),
                Tables\Columns\TextColumn::make('currency')
                    ->label('Currency'),
                Tables\Columns\TextColumn::make('account.name')
                    ->label('Account')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('method')
                    ->label('Method'),
                Tables\Columns\TextColumn::make('reference')
                    ->label('Reference'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add payment'),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Add payment'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('No payments yet')
            ->emptyStateDescription('Add a payment installment for this project.');
    }
}

