<?php

namespace App\Filament\Resources\Advances\Pages;

use App\Filament\Resources\Advances\AdvanceResource;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdvances extends ListRecords
{
    protected static string $resource = AdvanceResource::class;
    protected static ?string $title = 'Liste des Acomptes';
    protected static ?string $breadcrumb = 'Liste des Acomptes';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Phosphor::PlusCircle)
                ->label('Nouvelle Acompte'),
        ];
    }
}
