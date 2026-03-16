<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Advances\AdvanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class AdvancesRelationManager extends RelationManager
{
    protected static string $relationship = 'Advances';

    protected static ?string $relatedResource = AdvanceResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
