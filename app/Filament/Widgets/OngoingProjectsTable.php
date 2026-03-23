<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OngoingProjectsTable extends BaseWidget
{
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        $query = Project::query()
            ->where('status', 'active')
            ->orderByDesc('start_date');

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Project')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->formatStateUsing(fn ($state, Project $record) => ($record->currency ?? 'PKR') . ' ' . number_format((float) $state, 2)),
                Tables\Columns\TextColumn::make('total_paid')
                    ->label('Paid')
                    ->formatStateUsing(fn ($state, Project $record) => ($record->currency ?? 'PKR') . ' ' . number_format((float) $record->total_paid, 2)),
                Tables\Columns\TextColumn::make('remaining_amount')
                    ->label('Remaining')
                    ->formatStateUsing(fn ($state, Project $record) => ($record->currency ?? 'PKR') . ' ' . number_format((float) $record->remaining_amount, 2)),
                Tables\Columns\TextColumn::make('start_date')
                    ->date(),
            ])
            ->defaultSort('start_date', 'desc')
            ->paginated([5, 10, 25]);
    }
}

