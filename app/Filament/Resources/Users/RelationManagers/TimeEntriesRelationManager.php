<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\TimeEntries\TimeEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class TimeEntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'TimeEntries';

    protected static ?string $relatedResource = TimeEntryResource::class;

    protected static ?string $title = 'Feuille d\'heure';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Liste des entrées de planning')
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
