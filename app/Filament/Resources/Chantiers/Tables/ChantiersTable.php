<?php

namespace App\Filament\Resources\Chantiers\Tables;

use App\Models\Chantier;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ChantiersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Aucun chantier enregistrer actuellement')
            ->emptyStateIcon(Phosphor::QuestionBold)
            ->emptyStateActions([
                CreateAction::make('create')
                    ->label("Créer un chantier")
                    ->icon(Phosphor::PlusCircleBold),
            ])
            ->columns([
                TextColumn::make('name')
                    ->label('Chantier')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('adresse')
                    ->label('Adresse')
                    ->limit(30),

                TextColumn::make('distance_km')
                    ->label('Distance')
                    ->suffix(' Km')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->defaultSort('name')
            ->filters([
                TernaryFilter::make('is_active'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('activate')
                        ->label('Activer')
                        ->color('success')
                        ->icon(Phosphor::Power)
                        ->action(fn(Chantier $record) => $record->activate())
                        ->visible(fn(Chantier $record): bool => !$record->is_active),

                    Action::make('deactivate')
                        ->label('Désactiver')
                        ->color('danger')
                        ->icon(Phosphor::Power)
                        ->action(fn(Chantier $record) => $record->deactivate())
                        ->visible(fn(Chantier $record): bool => $record->is_active),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
