<?php

namespace App\Filament\Resources\Chantiers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ChantierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('name')
                                ->label('Nom du chantier')
                                ->required(),

                            TextInput::make('adresse')
                                ->label('Adresse complète')
                                ->required()
                                ->helperText('L\'API Google calculera la distance automatiquement après l\'enregistrement.'),

                            Toggle::make('is_active')
                                ->label('Chantier actif')
                                ->default(true),
                        ]),
                    ])
            ]);
    }
}
