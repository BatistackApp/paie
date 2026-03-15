<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Absences\AbsenceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class AbsencesRelationManager extends RelationManager
{
    protected static string $relationship = 'Absences';

    protected static ?string $relatedResource = AbsenceResource::class;

    protected static ?string $title = 'Absences';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Liste des absences')
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
