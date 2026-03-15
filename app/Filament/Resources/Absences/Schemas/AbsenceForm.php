<?php

namespace App\Filament\Resources\Absences\Schemas;

use App\Enums\AbsenceType;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AbsenceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Détail de l\'absence')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make()
                            ->schema([
                                Select::make('user_id')
                                    ->options(fn () => User::where('is_salarie', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('absence_type')
                                    ->label('Type')
                                    ->options(AbsenceType::class)
                                    ->required(),
                            ]),

                        Grid::make()
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label('Date de début')
                                    ->required(),

                                DatePicker::make('end_date')
                                    ->label('Date de fin')
                                    ->required(),
                            ]),

                        Textarea::make('comment')
                            ->label('Commentaire')
                            ->columnSpanFull(),

                        Toggle::make('is_validated')
                            ->label('Justificatif reçu / Absence validée'),
                    ]),
            ]);
    }
}
