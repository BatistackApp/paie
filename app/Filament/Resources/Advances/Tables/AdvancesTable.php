<?php

namespace App\Filament\Resources\Advances\Tables;

use App\Enums\AdvanceType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdvancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Aucun acompte de saisie actuellement')
            ->columns([
                TextColumn::make('user.name')
                    ->label('salarie')
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('Montant')
                    ->money('EUR')
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('user.name')
                    ->label('salarie')
                    ->relationship('user', 'name'),

                SelectFilter::make('type')
                    ->label('Type')
                    ->options(AdvanceType::class),

                Filter::make('date')
                    ->schema([
                        DatePicker::make('from')->label('Du'),
                        DatePicker::make('until')->label('Au'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'], fn ($q) => $q->whereDate('date', '>=', $data['from']))
                        ->when($data['until'], fn ($q) => $q->whereDate('date', '<=', $data['until']))),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
