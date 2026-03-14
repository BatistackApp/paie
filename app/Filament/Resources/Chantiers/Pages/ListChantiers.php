<?php

namespace App\Filament\Resources\Chantiers\Pages;

use App\Filament\Resources\Chantiers\ChantierResource;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChantiers extends ListRecords
{
    protected static string $resource = ChantierResource::class;
    protected static ?string $navigationLabel = 'Liste des Chantiers';

    protected static ?string $breadcrumb = 'Liste des Chantiers';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Créer un chantier')->icon(Phosphor::PlusCircle),
        ];
    }
}
