<?php

namespace App\Filament\Resources\TimeEntries\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TimeEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Affectation')
                            ->columnSpan(1)
                            ->schema([
                                Select::make('user_id')
                                    ->label('Salarié')
                                    ->options(fn () => User::where('is_salarie', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('chantier_id')
                                    ->relationship('chantier', 'name')
                                    ->label('Chantier')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                DatePicker::make('entry_date')
                                    ->label('Date')
                                    ->required()
                                    ->default(now()),
                            ]),

                        Section::make('Horaire de la journée')
                            ->columnSpan(1)
                            ->columns(2)
                            ->schema([
                                TimePicker::make('depart_depot')
                                    ->label('Départ Dépot'),

                                TimePicker::make('embauche_chantier')
                                    ->label('Embauche sur site')
                                    ->required(),

                                TimePicker::make('debauche_chantier')
                                    ->label('Débauche sur site')
                                    ->required(),

                                TimePicker::make('retour_depot')
                                    ->label('Retour Dépot'),

                                TextInput::make('break_duration_minute')
                                    ->label('Pause (min)')
                                    ->numeric()
                                    ->default(60)
                                    ->suffix('min'),
                            ]),

                        Section::make('Indemnités et Validations')
                        ->columnSpan(1)
                        ->schema([
                            Toggle::make('has_meal')
                            ->label('Panier Repas')
                            ->default(true),

                            Toggle::make('has_night')
                            ->label('Nuitée / Grand Déplacement'),

                            Toggle::make('is_validated')
                            ->label('Ligne validée pour la paye'),
                        ]),
                    ]),
            ]);
    }
}
