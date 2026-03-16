<?php

namespace App\Filament\Resources\TimeEntries\Tables;

use App\Models\TimeEntry;
use App\Models\User;
use App\Service\CcnCalculatorService;
use App\Service\PdfService;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TimeEntriesTable
{
    public static function configure(Table $table): Table
    {
        $calculator = app(CcnCalculatorService::class);

        return $table
            ->emptyStateHeading('Aucune entrée de disponible')
            ->emptyStateIcon(Phosphor::CalendarSlash)
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Ajouter une entrée')
                    ->icon(Phosphor::CalendarPlus),
            ])
            ->columns([
                TextColumn::make('entry_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Salarié'),

                TextColumn::make('chantier.name')
                    ->label('Chantier'),

                TextColumn::make('work_duration')
                    ->label('Travail (H)')
                    ->getStateUsing(fn (TimeEntry $record) => $calculator->calculateWorkDuration($record))
                    ->summarize(
                        Summarizer::make()
                            ->label('Total Travail')
                            ->suffix('H')
                            ->using(fn ($query) => TimeEntry::hydrate($query->get()->toArray())->sum(fn ($record) => $calculator->calculateWorkDuration($record)))
                    )
                    ->badge()
                    ->color('success'),

                TextColumn::make('travel_duration')
                    ->label('Trajet (H)')
                    ->getStateUsing(fn (TimeEntry $record) => $calculator->calculateTravelDuration($record))
                    ->summarize(
                        Summarizer::make()
                            ->label('Total Trajet')
                            ->suffix('H')
                            ->using(fn ($query) => TimeEntry::hydrate($query->get()->toArray())->sum(fn ($record) => $calculator->calculateTravelDuration($record)))
                    )
                    ->badge()
                    ->color('info'),

                TextColumn::make('zone')
                    ->label('Zone CCN')
                    ->getStateUsing(fn (TimeEntry $record) => $calculator->determineCcnZone($record->chantier->distance_km ?? 0))
                    ->description(fn (TimeEntry $record) => ($record->chantier->distance_km ?? 0).' km')
                    ->color(fn ($state) => $state === 'Grand Déplacement' ? 'danger' : 'gray'),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->options(fn () => User::where('is_salarie', true)->pluck('name', 'id'))
                    ->label('Salarié'),

                Filter::make('entry_date')
                    ->schema([
                        DatePicker::make('from')->label('Du'),
                        DatePicker::make('until')->label('Au'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'], fn ($q) => $q->whereDate('entry_date', '>=', $data['from']))
                        ->when($data['until'], fn ($q) => $q->whereDate('entry_date', '<=', $data['until']))),

            ])
            ->headerActions([
                Action::make('print_pdf')
                    ->label('Imprimer le relevé')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->action(function (HasTable $livewire) {
                        $entries = $livewire->getFilteredTableQuery()->with(['user', 'chantier'])->get();

                        $pdfContent = app(PdfService::class)->generateFromView('pdf.relever_heure', [
                            'entries' => $entries,
                            'title' => 'Relevé d\'heures de travail',
                        ]);

                        return response()->streamDownload(
                            fn () => print ($pdfContent),
                            'releve_heures_'.now()->format('Y-m-d').'.pdf'
                        );
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->hidden(fn (TimeEntry $record) => $record->is_validated),

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
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
