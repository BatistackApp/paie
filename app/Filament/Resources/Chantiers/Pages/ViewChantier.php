<?php

namespace App\Filament\Resources\Chantiers\Pages;

use App\Filament\Resources\Chantiers\ChantierResource;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewChantier extends ViewRecord
{
    protected static string $resource = ChantierResource::class;

    protected static ?string $navigationLabel = "Fiche du chantier";

    protected static ?string $breadcrumb = 'Fiche du chantier';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Editer le chantier')->icon(Phosphor::PencilCircle),
        ];
    }
}
