<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;
    protected static ?string $title = 'Liste des salariés';
    protected static ?string $breadcrumb = 'Liste des salariés';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Phosphor::UserPlus)
                ->label('Nouveau salarié'),
        ];
    }
}
