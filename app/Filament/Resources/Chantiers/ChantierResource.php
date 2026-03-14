<?php

namespace App\Filament\Resources\Chantiers;

use App\Filament\Resources\Chantiers\Pages\CreateChantier;
use App\Filament\Resources\Chantiers\Pages\EditChantier;
use App\Filament\Resources\Chantiers\Pages\ListChantiers;
use App\Filament\Resources\Chantiers\Pages\ViewChantier;
use App\Filament\Resources\Chantiers\Schemas\ChantierForm;
use App\Filament\Resources\Chantiers\Schemas\ChantierInfolist;
use App\Filament\Resources\Chantiers\Tables\ChantiersTable;
use App\Models\Chantier;
use BackedEnum;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ChantierResource extends Resource
{
    protected static ?string $model = Chantier::class;

    protected static string|BackedEnum|null $navigationIcon = Phosphor::CraneBold;

    protected static string|UnitEnum|null $navigationGroup = 'Référentiel';

    public static function form(Schema $schema): Schema
    {
        return ChantierForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ChantierInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChantiersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChantiers::route('/'),
            'create' => CreateChantier::route('/create'),
            'view' => ViewChantier::route('/{record}'),
            'edit' => EditChantier::route('/{record}/edit'),
        ];
    }
}
