<?php

namespace App\Filament\Resources\Projects\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->formatStateUsing(fn ($state, $record) => ($record->currency ?? 'PKR') . ' ' . number_format((float) $state, 2)),
                TextColumn::make('total_paid')
                    ->label('Paid')
                    ->formatStateUsing(fn ($state, $record) => ($record->currency ?? 'PKR') . ' ' . number_format((float) $record->total_paid, 2)),
                TextColumn::make('remaining_amount')
                    ->label('Remaining')
                    ->formatStateUsing(fn ($state, $record) => ($record->currency ?? 'PKR') . ' ' . number_format((float) $record->remaining_amount, 2)),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'active',
                        'warning' => 'completed',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}

