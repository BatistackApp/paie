<?php

namespace App\Filament\Resources\Absences\Tables;

use App\Models\User;
use App\Service\PdfService;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AbsencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Aucune absences enregistrer actuellement')
            ->emptyStateIcon(Phosphor::CalendarSlash)
            ->emptyStateActions([
                CreateAction::make()
                    ->icon(Phosphor::CalendarPlus)
                    ->label('Ajouter une absence'),
            ])
            ->columns([
                TextColumn::make('user.name')
                    ->label('Salarié')
                    ->searchable(),

                TextColumn::make('absence_type')
                    ->label('Type')
                    ->badge(),

                TextColumn::make('start_date')
                    ->label('Du')
                    ->date('d/m/Y'),

                TextColumn::make('end_date')
                    ->label('Au')
                    ->date('d/m/Y'),

                IconColumn::make('is_validated')
                    ->label('Valider')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('user.name')
                    ->label('Salarié')
                    ->options(fn () => User::where('is_salarie', true)->pluck('name', 'id')),

                TernaryFilter::make('is_validated')
                    ->label('Valider'),

                Filter::make('periode')
                    ->label("Période d'absence")
                    ->schema([
                        DatePicker::make('start_at')
                            ->label('A partir de'),

                        DatePicker::make('end_at')
                            ->label('Jusqu\'au'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['start_at'], fn ($q) => $q->whereDate('start_date', '>=', $data['start_at']))
                        ->when($data['end_at'], fn ($q) => $q->whereDate('end_date', '<=', $data['end_at']))),
            ])
            ->headerActions([
                // Action d'impression PDF pour les absences avec correction HasTable
                Action::make('print_absences')
                    ->label('Imprimer les absences')
                    ->icon('heroicon-o-printer')
                    ->color('danger')
                    ->action(function (HasTable $livewire) {
                        $absences = $livewire->getFilteredTableQuery()->with('user')->get();

                        $pdfContent = app(PdfService::class)->generateFromView('pdf.relever_absence', [
                            'absences' => $absences,
                            'title' => 'Récapitulatif des absences'
                        ]);

                        return response()->streamDownload(
                            fn () => print($pdfContent),
                            'releve_absences_' . now()->format('Y-m-d') . '.pdf'
                        );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
