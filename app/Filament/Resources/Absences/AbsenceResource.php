<?php

namespace App\Filament\Resources\Absences;

use App\Filament\Resources\Absences\Pages\CreateAbsence;
use App\Filament\Resources\Absences\Pages\EditAbsence;
use App\Filament\Resources\Absences\Pages\ListAbsences;
use App\Filament\Resources\Absences\Pages\ViewAbsence;
use App\Filament\Resources\Absences\Schemas\AbsenceForm;
use App\Filament\Resources\Absences\Schemas\AbsenceInfolist;
use App\Filament\Resources\Absences\Tables\AbsencesTable;
use App\Models\Absence;
use BackedEnum;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class AbsenceResource extends Resource
{
    protected static ?string $model = Absence::class;

    protected static string|BackedEnum|null $navigationIcon = Phosphor::Prohibit;

    protected static string|UnitEnum|null $navigationGroup = 'Resources Humaines';

    public static function form(Schema $schema): Schema
    {
        return AbsenceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AbsenceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AbsencesTable::configure($table);
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
            'index' => ListAbsences::route('/'),
            'create' => CreateAbsence::route('/create'),
            'view' => ViewAbsence::route('/{record}'),
            'edit' => EditAbsence::route('/{record}/edit'),
        ];
    }
}
