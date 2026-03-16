<?php

namespace App\Filament\RelationManagers;

use App\Services\FilamentComponentService;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectExpensesRelationManager extends RelationManager
{
    protected static string $relationship = 'expenses';

    protected static ?string $title = 'Project Expenses';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('spent_at')
                ->default(now())
                ->required(),
            TextInput::make('amount')
                ->numeric()
                ->required(),
            Select::make('currency')
                ->options(['USD' => 'USD', 'PKR' => 'PKR'])
                ->default('USD')
                ->required(),
            Select::make('account_id')
                ->label('Account')
                ->relationship('account', 'name', modifyQueryUsing: fn (Builder $query) => $query->orderBy('name'))
                ->searchable()
                ->preload(),
            Select::make('expense_category_id')
                ->label('Category')
                ->relationship('expenseCategory', 'name', modifyQueryUsing: fn (Builder $query) => $query->orderBy('name'))
                ->searchable()
                ->preload(),
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
            FilamentComponentService::getMediaComponents('expense_receipts'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('spent_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expenseCategory.name')
                    ->label('Category')
                    ->badge(),
                Tables\Columns\TextColumn::make('payee_name')
                    ->label('Payee'),
                Tables\Columns\TextColumn::make('account.name')
                    ->label('Account')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn ($state, $record) => ($record->currency ?? 'USD') . ' ' . number_format((float) $state, 2))
                    ->label('Amount'),
                Tables\Columns\TextColumn::make('currency')
                    ->label('Currency'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add expense'),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Add expense'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('No expenses yet')
            ->emptyStateDescription('Add an expense for this project.');
    }
}

