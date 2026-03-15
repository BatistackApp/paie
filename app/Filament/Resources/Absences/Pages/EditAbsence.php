<?php

namespace App\Filament\Resources\Absences\Pages;

use App\Filament\Resources\Absences\AbsenceResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAbsence extends EditRecord
{
    protected static string $resource = AbsenceResource::class;
    protected static ?string $title = 'Edition d\'absences';
    protected static ?string $breadcrumb = 'Edition d\'absences';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->label('Voir'),
            DeleteAction::make()->label('Supprimer'),
        ];
    }
}
