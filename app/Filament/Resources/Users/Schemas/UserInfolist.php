<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Identité')
                                    ->icon(Phosphor::UserCircle)
                                    ->color('primary'),

                                TextEntry::make('email')
                                    ->label('Email')
                                    ->color('primary')
                                    ->icon(Phosphor::Envelope)
                                    ->url(fn (User $user) => 'mailto:'.$user->email),

                                TextEntry::make('created_at')
                                    ->label('Inscrit le')
                                    ->date('d/m/Y')
                                    ->icon(Phosphor::Calendar)
                                    ->color('primary'),
                            ]),
                    ]),
            ]);
    }
}
