<?php

namespace App\Filament\Resources\Absences\Pages;

use App\Filament\Resources\Absences\AbsenceResource;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAbsences extends ListRecords
{
    protected static string $resource = AbsenceResource::class;
    protected static ?string $title = 'Liste des absences';
    protected static ?string $breadcrumb = 'Liste des absences';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Phosphor::CalendarPlus)
                ->label('Ajouter une absence'),
        ];
    }
}
