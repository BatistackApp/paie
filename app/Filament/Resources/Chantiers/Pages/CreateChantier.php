<?php

namespace App\Filament\Resources\Chantiers\Pages;

use App\Filament\Resources\Chantiers\ChantierResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChantier extends CreateRecord
{
    protected static string $resource = ChantierResource::class;
    protected static ?string $navigationLabel = 'Création du chantier';
    protected static ?string $breadcrumb = 'Création de chantier';
}
