<?php

namespace App\Filament\Resources\Advances\Pages;

use App\Filament\Resources\Advances\AdvanceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdvance extends CreateRecord
{
    protected static string $resource = AdvanceResource::class;
    protected static ?string $title = 'Nouvelle Acompte';
    protected static ?string $breadcrumb = 'Nouvelle Acompte';
}
