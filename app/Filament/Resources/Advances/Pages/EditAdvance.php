<?php

namespace App\Filament\Resources\Advances\Pages;

use App\Filament\Resources\Advances\AdvanceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdvance extends EditRecord
{
    protected static string $resource = AdvanceResource::class;
    protected static ?string $title = 'Edition d\'un Acompte';
    protected static ?string $breadcrumb = 'Edition d\'un Acompte';
}
