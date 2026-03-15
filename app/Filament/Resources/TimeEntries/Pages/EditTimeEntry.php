<?php

namespace App\Filament\Resources\TimeEntries\Pages;

use App\Filament\Resources\TimeEntries\TimeEntryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTimeEntry extends EditRecord
{
    protected static string $resource = TimeEntryResource::class;
    protected static ?string $title = 'Editer une entrée';

    protected static ?string $breadcrumb = 'Editer une entrée';
}
