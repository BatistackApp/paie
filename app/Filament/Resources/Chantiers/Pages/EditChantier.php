<?php

namespace App\Filament\Resources\Chantiers\Pages;

use App\Filament\Resources\Chantiers\ChantierResource;
use App\Models\Chantier;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditChantier extends EditRecord
{
    protected static string $resource = ChantierResource::class;

    protected static ?string $navigationLabel = 'Edition du chantier';

    protected static ?string $breadcrumb = 'Edition de chantier';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->label('Voir')->icon(Phosphor::Eye),
            DeleteAction::make()->label('Supprimer')->icon(Phosphor::Trash),
            Action::make('activate')
                ->label('Activer')
                ->color('success')
                ->icon(Phosphor::Power)
                ->action(fn (Chantier $record) => $record->activate())
                ->visible(fn (Chantier $record): bool => ! $record->is_active),

            Action::make('deactivate')
                ->label('Désactiver')
                ->color('danger')
                ->icon(Phosphor::Power)
                ->action(fn (Chantier $record) => $record->deactivate())
                ->visible(fn (Chantier $record): bool => $record->is_active),
        ];
    }
}
