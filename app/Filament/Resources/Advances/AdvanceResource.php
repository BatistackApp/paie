<?php

namespace App\Filament\Resources\Advances;

use App\Filament\Resources\Advances\Pages\CreateAdvance;
use App\Filament\Resources\Advances\Pages\EditAdvance;
use App\Filament\Resources\Advances\Pages\ListAdvances;
use App\Filament\Resources\Advances\Schemas\AdvanceForm;
use App\Filament\Resources\Advances\Tables\AdvancesTable;
use App\Models\Advance;
use BackedEnum;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class AdvanceResource extends Resource
{
    protected static ?string $model = Advance::class;

    protected static string|BackedEnum|null $navigationIcon = Phosphor::Bank;

    protected static string|UnitEnum|null $navigationGroup = 'Resources Humaines';

    protected static ?string $label = 'Acomptes';

    protected static ?string $breadcrumb = 'Acomptes';

    public static function form(Schema $schema): Schema
    {
        return AdvanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdvancesTable::configure($table);
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
            'index' => ListAdvances::route('/'),
            'create' => CreateAdvance::route('/create'),
            'edit' => EditAdvance::route('/{record}/edit'),
        ];
    }
}
