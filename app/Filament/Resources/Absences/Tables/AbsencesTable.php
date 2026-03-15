<?php

namespace App\Filament\Resources\Absences\Tables;

use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
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
                    ->relationship('user', 'name'),

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
