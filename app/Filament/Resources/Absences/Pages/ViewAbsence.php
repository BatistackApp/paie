<?php

namespace App\Filament\Resources\Absences\Pages;

use App\Filament\Resources\Absences\AbsenceResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAbsence extends ViewRecord
{
    protected static string $resource = AbsenceResource::class;

    protected static ?string $title = 'Fiche d\'absence';
    protected static ?string $breadcrumb = 'Fiche d\'absence';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Modifier'),
        ];
    }
}
