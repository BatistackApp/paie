<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations Salarié')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom/Prénom')
                            ->required(),

                        TextInput::make('email')
                            ->label('Email Professionnel')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required(),

                        TextInput::make('password')
                            ->label('Mot de passe')
                            ->password()
                            ->revealable()
                            ->default(Str::random(10))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context) => $context === 'create'),
                    ]),
            ]);
    }
}
