<?php

namespace App\Filament\Resources\Expenses\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('spent_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('category')
                    ->badge(),
                TextColumn::make('payee_name')
                    ->label('Payee'),
                TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable(),
                TextColumn::make('amount')
                    ->formatStateUsing(fn ($state, $record) => ($record->currency ?? 'USD') . ' ' . number_format((float) $state, 2))
                    ->label('Amount'),
                TextColumn::make('currency')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}

