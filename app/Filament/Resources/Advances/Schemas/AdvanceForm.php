<?php

namespace App\Filament\Resources\Advances\Schemas;

use App\Enums\AdvanceType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AdvanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Détail de l\'acompte')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->label('Salarié')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                TextInput::make('amount')
                                    ->label('Montant')
                                    ->numeric()
                                    ->prefix('€')
                                    ->required(),

                                TextInput::make('date')
                                    ->label('Date')
                                    ->default(now())
                                    ->required(),

                                Select::make('type')
                                    ->label('Type d\'acompte')
                                    ->options(AdvanceType::class)
                                    ->required(),
                            ]),

                        Textarea::make('reason')
                            ->label('Motif / Commentaire')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
