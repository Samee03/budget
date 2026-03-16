<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Project;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ProjectForm
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
                                Select::make('client_id')
                                    ->label('Client')
                                    ->relationship('client', 'name', modifyQueryUsing: fn (Builder $query) => $query->orderBy('name'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('name')
                                    ->label('Project name')
                                    ->required(),
                                Textarea::make('description')
                                    ->rows(3),
                                TextInput::make('total_amount')
                                    ->numeric()
                                    ->required(),
                                Select::make('currency')
                                    ->options([
                                        'PKR' => 'PKR',
                                        'USD' => 'USD',
                                    ])
                                    ->default('PKR')
                                    ->required(),
                                DatePicker::make('start_date')
                                    ->required(),
                                DatePicker::make('end_date')
                                    ->hint('Leave empty for ongoing')
                                    ->after('start_date'),
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'active' => 'Active',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('active'),
                                Textarea::make('notes')
                                    ->rows(3),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }
}

