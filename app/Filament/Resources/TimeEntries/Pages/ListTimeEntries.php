<?php

namespace App\Filament\Resources\TimeEntries\Pages;

use App\Filament\Resources\TimeEntries\TimeEntryResource;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTimeEntries extends ListRecords
{
    protected static string $resource = TimeEntryResource::class;
    protected static ?string $title = 'Liste des entrées';

    protected static ?string $breadcrumb = 'Liste des entrées';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Ajouter une entrée')
                ->icon(Phosphor::CalendarPlus),
        ];
    }
}
