<?php

namespace App\Filament\Resources\TimeEntries\Pages;

use App\Filament\Resources\TimeEntries\TimeEntryResource;
use App\Models\TimeEntry;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTimeEntry extends ViewRecord
{
    protected static string $resource = TimeEntryResource::class;

    protected static ?string $title = 'Fiche d\'une entrée';

    protected static ?string $breadcrumb = 'Fiche d\'une entrée';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->icon(Phosphor::CalendarFill)
                ->label('Modifier'),

            DeleteAction::make()
                ->icon(Phosphor::CalendarX)
                ->label('Supprimer'),

            Action::make('validated')
                ->icon(Phosphor::CalendarCheck)
                ->label('Valider')
                ->color('success')
                ->visible(fn (TimeEntry $record): bool => ! $record->is_validated)
                ->action(fn (TimeEntry $record) => $record->validate()),

            Action::make('unvalidated')
                ->icon(Phosphor::CalendarMinus)
                ->label('Dévalider')
                ->color('danger')
                ->visible(fn (TimeEntry $record): bool => $record->is_validated)
                ->action(fn (TimeEntry $record) => $record->unvalidate()),
        ];
    }
}
